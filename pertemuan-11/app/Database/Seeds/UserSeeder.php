<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Akun demo (data fiktif - jangan dipakai di produksi!):
 *   admin@shopease.test      / Admin123!
 *   operator@shopease.test   / Operator123!
 *   customer1@shopease.test  / Customer123!
 *   customer2@shopease.test  / Customer123!
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        if ($this->db->table('users')->countAllResults() > 0) {
            return;
        }

        $now   = date('Y-m-d H:i:s');
        $users = [
            ['Administrator ShopEase', 'admin@shopease.test', 'Admin123!', 'admin', '081200000001'],
            ['Operator Toko', 'operator@shopease.test', 'Operator123!', 'operator', '081200000002'],
            ['Budi Santoso', 'customer1@shopease.test', 'Customer123!', 'customer', '081200000011'],
            ['Siti Rahma', 'customer2@shopease.test', 'Customer123!', 'customer', '081200000012'],
        ];

        foreach ($users as [$name, $email, $password, $role, $phone]) {
            $this->db->table('users')->insert([
                'name'       => $name,
                'email'      => $email,
                'password'   => password_hash($password, PASSWORD_DEFAULT),
                'role'       => $role,
                'phone'      => $phone,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
