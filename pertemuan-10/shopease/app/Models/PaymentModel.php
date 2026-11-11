<?php

namespace App\Models;

class PaymentModel extends JsonModel
{
    /** Status pembayaran PRD 9.2 */
    public const STATUSES = ['unpaid', 'submitted', 'verified', 'rejected'];

    protected string $table = 'payments';

    public function forOrder(int $orderId): ?array
    {
        $payment = $this->firstWhere(['order_id' => $orderId]);

        if ($payment === null) {
            return null;
        }

        $verifier = $payment['verified_by'] !== null ? model(UserModel::class)->find($payment['verified_by']) : null;

        return $payment + ['verifier_name' => $verifier['name'] ?? null];
    }
}
