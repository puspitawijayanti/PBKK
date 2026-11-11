<?php

namespace App\Models;

class UserModel extends JsonModel
{
    public const ROLES = ['customer', 'admin', 'operator'];

    protected string $table = 'users';

    /**
     * Password selalu disimpan sebagai hash (NFR-003). Kirim `password`, yang disimpan hanya `password_hash`.
     */
    public function insert(array $data): int
    {
        return parent::insert($this->hashPassword($data) + ['role' => 'customer', 'phone' => null, 'is_active' => true]);
    }

    public function update(int $id, array $data): void
    {
        parent::update($id, $this->hashPassword($data));
    }

    public function findByEmail(string $email): ?array
    {
        return $this->firstWhere(['email' => strtolower(trim($email))]);
    }

    public function search(?string $keyword, ?string $role): array
    {
        $keyword = trim((string) $keyword);
        $rows    = array_filter($this->all(), static fn (array $u): bool => (self::contains($u['name'], $keyword) || self::contains($u['email'], $keyword))
            && (! in_array($role, self::ROLES, true) || $u['role'] === $role));

        return self::sortBy(array_values($rows), [['created_at', 'desc'], ['id', 'desc']]);
    }

    private function hashPassword(array $data): array
    {
        if (isset($data['password'])) {
            if ($data['password'] !== '') {
                $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            unset($data['password']);
        }

        if (isset($data['email'])) {
            $data['email'] = strtolower(trim($data['email']));
        }

        return $data;
    }
}
