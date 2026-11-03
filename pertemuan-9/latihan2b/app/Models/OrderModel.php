<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    public const STATUSES = ['awaiting_payment', 'awaiting_verification', 'processing', 'shipped', 'completed', 'cancelled'];

    public const FLOW = [
        'awaiting_payment'      => ['awaiting_verification', 'cancelled'],
        'awaiting_verification' => ['processing', 'awaiting_payment', 'cancelled'],
        'processing'            => ['shipped', 'cancelled'],
        'shipped'               => ['completed'],
        'completed'             => [],
        'cancelled'             => [],
    ];

    public const PAID_STATUSES = ['processing', 'shipped', 'completed'];

    protected $table         = 'orders';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'user_id', 'code', 'status', 'recipient_name', 'phone', 'address', 'city', 'postal_code', 'note',
        'subtotal', 'shipping_cost', 'total', 'payment_proof', 'courier', 'tracking_number',
        'paid_at', 'verified_at', 'shipped_at', 'completed_at', 'cancelled_at',
    ];
    protected $useTimestamps = true;

    public static function canMove(string $from, string $to): bool
    {
        return in_array($to, self::FLOW[$from] ?? [], true);
    }

    public function withBuyer(): static
    {
        return $this->select('orders.*, users.name AS buyer_name, users.email AS buyer_email')
            ->join('users', 'users.id = orders.user_id');
    }

    public function search(?string $keyword, ?string $status): static
    {
        $this->withBuyer();

        if ($keyword !== null && $keyword !== '') {
            $this->groupStart()
                ->like('orders.code', $keyword)
                ->orLike('users.name', $keyword)
                ->orLike('orders.recipient_name', $keyword)
                ->groupEnd();
        }

        if (in_array($status, self::STATUSES, true)) {
            $this->where('orders.status', $status);
        }

        return $this->orderBy('orders.created_at', 'DESC');
    }

    public function findOwnedByCode(string $code, int $userId): ?array
    {
        return $this->where('code', $code)->where('user_id', $userId)->first();
    }

    public function countByStatus(): array
    {
        $counts = array_fill_keys(self::STATUSES, 0);
        $rows = $this->db->table($this->table)->select('status, COUNT(*) AS total')->groupBy('status')->get()->getResultArray();

        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['total'];
        }

        return $counts;
    }

    public function revenue(?string $since = null): int
    {
        $builder = $this->db->table($this->table)->selectSum('total')->whereIn('status', self::PAID_STATUSES);

        if ($since !== null) {
            $builder->where('created_at >=', $since);
        }

        return (int) $builder->get()->getRow()->total;
    }

    public function monthlyRevenue(int $months = 6): array
    {
        $start = date('Y-m-01', strtotime('-' . ($months - 1) . ' months'));
        $rows = $this->db->table($this->table)
            ->select("DATE_FORMAT(created_at, '%Y-%m') AS period, SUM(total) AS revenue, COUNT(*) AS orders", false)
            ->whereIn('status', self::PAID_STATUSES)
            ->where('created_at >=', $start)
            ->groupBy('period')
            ->get()
            ->getResultArray();

        $indexed = array_column($rows, null, 'period');
        $result = [];

        for ($i = 0; $i < $months; $i++) {
            $period = date('Y-m', strtotime($start . ' +' . $i . ' months'));
            $result[$period] = [
                'revenue' => (int) ($indexed[$period]['revenue'] ?? 0),
                'orders'  => (int) ($indexed[$period]['orders'] ?? 0),
            ];
        }

        return $result;
    }

    public function generateCode(): string
    {
        $prefix = 'KN-' . date('Ymd') . '-';
        $last = $this->db->table($this->table)->select('code')->like('code', $prefix, 'after')->orderBy('code', 'DESC')->get(1)->getRow();
        $next = $last === null ? 1 : (int) substr($last->code, -4) + 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
