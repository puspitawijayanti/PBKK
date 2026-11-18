<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['name', 'email', 'password', 'role', 'phone', 'is_active'];
    protected $useTimestamps    = true;

    public const ROLES = ['admin', 'operator', 'customer'];

    /** Daftar customer + jumlah pesanan (untuk halaman admin). */
    public function customersWithStats(?string $search = null)
    {
        $builder = $this->select('users.*, COUNT(orders.id) AS total_orders, COALESCE(SUM(orders.total), 0) AS total_spent')
            ->join('orders', "orders.user_id = users.id AND orders.order_status <> 'cancelled'", 'left')
            ->where('users.role', 'customer')
            ->groupBy('users.id');

        if ($search !== null && $search !== '') {
            $builder->groupStart()
                ->like('users.name', $search)
                ->orLike('users.email', $search)
                ->groupEnd();
        }

        return $builder->orderBy('users.created_at', 'DESC');
    }
}
