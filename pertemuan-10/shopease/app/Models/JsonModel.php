<?php

namespace App\Models;

use App\Libraries\JsonStore;

/**
 * Model dasar untuk satu "tabel" di JsonStore. Struktur kolom mengikuti rancangan tabel PRD bagian 11.
 */
abstract class JsonModel
{
    protected string $table;

    protected function store(): JsonStore
    {
        return service('store');
    }

    /**
     * @return list<array>
     */
    public function all(): array
    {
        return array_values($this->store()->all($this->table));
    }

    public function find(int|string|null $id): ?array
    {
        return $id === null ? null : $this->store()->find($this->table, (int) $id);
    }

    public function insert(array $data): int
    {
        return $this->store()->insert($this->table, $data);
    }

    public function update(int $id, array $data): void
    {
        $this->store()->update($this->table, $id, $data);
    }

    public function delete(int $id): void
    {
        $this->store()->delete($this->table, $id);
    }

    /**
     * Baris yang semua kolomnya sama dengan $conditions, misalnya where(['user_id' => 3]).
     *
     * @return list<array>
     */
    public function where(array $conditions): array
    {
        return array_values(array_filter($this->all(), static function (array $row) use ($conditions): bool {
            foreach ($conditions as $field => $value) {
                if (($row[$field] ?? null) !== $value) {
                    return false;
                }
            }

            return true;
        }));
    }

    public function firstWhere(array $conditions): ?array
    {
        return $this->where($conditions)[0] ?? null;
    }

    public function count(array $conditions = []): int
    {
        return count($conditions === [] ? $this->all() : $this->where($conditions));
    }

    /**
     * Mengurutkan baris berdasarkan beberapa kolom, misalnya [['created_at', 'desc'], ['id', 'desc']].
     */
    protected static function sortBy(array $rows, array $orders): array
    {
        usort($rows, static function (array $a, array $b) use ($orders): int {
            foreach ($orders as [$field, $direction]) {
                $cmp = is_string($a[$field] ?? null) && ! is_numeric($a[$field])
                    ? strcasecmp($a[$field], (string) ($b[$field] ?? ''))
                    : ($a[$field] ?? 0) <=> ($b[$field] ?? 0);

                if ($cmp !== 0) {
                    return $direction === 'desc' ? -$cmp : $cmp;
                }
            }

            return 0;
        });

        return $rows;
    }

    protected static function contains(?string $haystack, string $needle): bool
    {
        return $needle === '' || mb_stripos((string) $haystack, $needle) !== false;
    }
}
