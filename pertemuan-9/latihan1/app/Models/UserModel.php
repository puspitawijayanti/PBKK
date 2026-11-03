<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['name', 'username', 'password', 'role'];
    protected $useTimestamps    = true;
    protected $beforeInsert     = ['hashPassword'];
    protected $beforeUpdate     = ['hashPassword'];

    protected $validationRules = [
        'id'       => 'permit_empty|is_natural_no_zero',
        'name'     => 'required|min_length[3]|max_length[100]',
        'username' => 'required|alpha_dash|min_length[3]|max_length[50]|is_unique[users.username,id,{id}]',
        'role'     => 'required|in_list[admin,borrower]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Nama wajib diisi.',
            'min_length' => 'Nama minimal 3 karakter.',
            'max_length' => 'Nama maksimal 100 karakter.',
        ],
        'username' => [
            'required'   => 'Username wajib diisi.',
            'alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'min_length' => 'Username minimal 3 karakter.',
            'max_length' => 'Username maksimal 50 karakter.',
            'is_unique'  => 'Username sudah dipakai.',
        ],
        'role' => [
            'required' => 'Peran wajib dipilih.',
            'in_list'  => 'Peran tidak valid.',
        ],
    ];

    protected function hashPassword(array $data): array
    {
        if (! isset($data['data']['password'])) {
            return $data;
        }

        if ($data['data']['password'] === '') {
            unset($data['data']['password']);

            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);

        return $data;
    }

    public function search(?string $keyword, ?string $role): static
    {
        if ($keyword !== null && $keyword !== '') {
            $this->groupStart()->like('name', $keyword)->orLike('username', $keyword)->groupEnd();
        }

        if (in_array($role, ['admin', 'borrower'], true)) {
            $this->where('role', $role);
        }

        return $this->orderBy('role')->orderBy('name');
    }
}
