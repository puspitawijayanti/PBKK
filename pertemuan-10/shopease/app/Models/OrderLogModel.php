<?php

namespace App\Models;

/**
 * Riwayat perubahan status pesanan beserta pengguna yang mengubahnya (BR-012).
 */
class OrderLogModel extends JsonModel
{
    protected string $table = 'order_logs';

    public function forOrder(int $orderId): array
    {
        $users = model(UserModel::class);

        return array_map(static function (array $log) use ($users): array {
            $user = $log['user_id'] !== null ? $users->find($log['user_id']) : null;

            return $log + ['user_name' => $user['name'] ?? null, 'user_role' => $user['role'] ?? null];
        }, self::sortBy($this->where(['order_id' => $orderId]), [['id', 'asc']]));
    }
}
