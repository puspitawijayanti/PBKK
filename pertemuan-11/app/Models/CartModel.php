<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table         = 'carts';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id'];
    protected $useTimestamps = true;

    /** Ambil keranjang aktif milik user, buat jika belum ada. */
    public function getOrCreateForUser(int $userId): array
    {
        $cart = $this->where('user_id', $userId)->first();

        if (! $cart) {
            $id   = $this->insert(['user_id' => $userId], true);
            $cart = $this->find($id);
        }

        return $cart;
    }
}
