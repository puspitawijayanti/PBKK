<?php

namespace App\Models;

use CodeIgniter\Model;

class AddressModel extends Model
{
    protected $table         = 'addresses';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'user_id', 'recipient_name', 'phone', 'address',
        'city', 'province', 'postal_code', 'is_default',
    ];
    protected $useTimestamps = true;

    public function forUser(int $userId): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('is_default', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    public function findOwned(int $id, int $userId): ?array
    {
        return $this->where('id', $id)->where('user_id', $userId)->first();
    }

    /** Jadikan satu alamat sebagai default (yang lain di-reset). */
    public function setDefault(int $id, int $userId): void
    {
        $this->where('user_id', $userId)->set(['is_default' => 0])->update();
        $this->update($id, ['is_default' => 1]);
    }

    /** Format satu baris alamat untuk snapshot pada orders.shipping_address. */
    public static function formatLine(array $a): string
    {
        return sprintf(
            '%s (%s) - %s, %s, %s %s',
            $a['recipient_name'],
            $a['phone'],
            $a['address'],
            $a['city'],
            $a['province'],
            $a['postal_code']
        );
    }
}
