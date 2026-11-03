<?php

namespace App\Models;

use CodeIgniter\Model;

class LoanModel extends Model
{
    public const STATUSES = ['pending', 'approved', 'borrowed', 'returned', 'rejected'];

    public const TRANSITIONS = [
        'pending'  => ['approved', 'rejected'],
        'approved' => ['borrowed'],
        'borrowed' => ['returned'],
        'returned' => [],
        'rejected' => [],
    ];

    protected $table         = 'loans';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'tool_id', 'quantity', 'borrow_date', 'return_date', 'returned_at', 'status', 'note'];
    protected $useTimestamps = true;

    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }

    public function withRelations(): static
    {
        return $this->select('loans.*, users.name AS user_name, users.username, tools.name AS tool_name, tools.slug AS tool_slug, tools.photo AS tool_photo, tools.stock AS tool_stock')
            ->join('users', 'users.id = loans.user_id')
            ->join('tools', 'tools.id = loans.tool_id');
    }

    public function search(?string $keyword, ?string $status): static
    {
        $this->withRelations();

        if ($keyword !== null && $keyword !== '') {
            $this->groupStart()
                ->like('users.name', $keyword)
                ->orLike('users.username', $keyword)
                ->orLike('tools.name', $keyword)
                ->groupEnd();
        }

        if (in_array($status, self::STATUSES, true)) {
            $this->where('loans.status', $status);
        }

        return $this->orderBy('loans.created_at', 'DESC');
    }

    public function findOwned(int $loanId, int $userId): ?array
    {
        return $this->withRelations()
            ->where('loans.id', $loanId)
            ->where('loans.user_id', $userId)
            ->first();
    }

    public function countByStatus(?int $userId = null): array
    {
        $builder = $this->db->table($this->table)->select('status, COUNT(*) AS total')->groupBy('status');

        if ($userId !== null) {
            $builder->where('user_id', $userId);
        }

        $counts = array_fill_keys(self::STATUSES, 0);

        foreach ($builder->get()->getResultArray() as $row) {
            $counts[$row['status']] = (int) $row['total'];
        }

        return $counts;
    }

    public function monthlyTotals(int $months = 6): array
    {
        $start = date('Y-m-01', strtotime('-' . ($months - 1) . ' months'));
        $rows = $this->db->table($this->table)
            ->select("DATE_FORMAT(created_at, '%Y-%m') AS period, COUNT(*) AS total", false)
            ->where('created_at >=', $start)
            ->groupBy('period')
            ->get()
            ->getResultArray();

        $totals = array_column($rows, 'total', 'period');
        $result = [];

        for ($i = 0; $i < $months; $i++) {
            $period = date('Y-m', strtotime($start . ' +' . $i . ' months'));
            $result[$period] = (int) ($totals[$period] ?? 0);
        }

        return $result;
    }
}
