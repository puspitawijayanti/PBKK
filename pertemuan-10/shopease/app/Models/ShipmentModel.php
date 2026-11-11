<?php

namespace App\Models;

class ShipmentModel extends JsonModel
{
    public const STATUSES = ['preparing', 'shipped', 'delivered'];
    public const COURIERS = ['JNE', 'J&T Express', 'SiCepat', 'POS Indonesia', 'AnterAja'];

    protected string $table = 'shipments';

    public function forOrder(int $orderId): ?array
    {
        return $this->firstWhere(['order_id' => $orderId]);
    }
}
