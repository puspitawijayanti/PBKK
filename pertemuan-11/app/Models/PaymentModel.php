<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table         = 'payments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'order_id', 'method', 'amount', 'proof_file', 'payment_status',
        'note', 'verified_by', 'verified_at',
    ];
    protected $useTimestamps = true;

    public const STATUSES = ['unpaid', 'submitted', 'verified', 'rejected'];

    /** Daftar pembayaran + order + customer untuk halaman verifikasi admin. */
    public function withOrder()
    {
        return $this->select('payments.*, orders.order_number, orders.total AS order_total, orders.order_status,
                orders.order_date, users.name AS customer_name, verifier.name AS verifier_name')
            ->join('orders', 'orders.id = payments.order_id')
            ->join('users', 'users.id = orders.user_id')
            ->join('users AS verifier', 'verifier.id = payments.verified_by', 'left');
    }
}
