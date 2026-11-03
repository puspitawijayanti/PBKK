<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $borrowerPassword = password_hash('mahasiswa123', PASSWORD_DEFAULT);

        $users = [
            ['name' => 'Budi Santoso', 'username' => 'admin', 'password' => $adminPassword, 'role' => 'admin'],
            ['name' => 'Siti Rahmawati', 'username' => 'laboran2', 'password' => $adminPassword, 'role' => 'admin'],
        ];

        $borrowers = [
            ['5025231001', 'Andi Pratama'],
            ['5025231002', 'Bunga Lestari'],
            ['5025231003', 'Candra Wijaya'],
            ['5025231004', 'Dewi Anggraini'],
            ['5025231005', 'Eko Saputra'],
            ['5025231006', 'Fajar Nugroho'],
            ['5025231007', 'Gita Permata'],
            ['5025231008', 'Hendra Kurniawan'],
            ['5025231009', 'Indah Puspitasari'],
            ['5025231010', 'Joko Susilo'],
            ['5025231011', 'Kartika Sari'],
            ['5025231012', 'Lukman Hakim'],
        ];

        foreach ($borrowers as [$username, $name]) {
            $users[] = ['name' => $name, 'username' => $username, 'password' => $borrowerPassword, 'role' => 'borrower'];
        }

        foreach ($users as &$user) {
            $user['created_at'] = $now;
            $user['updated_at'] = $now;
        }

        $this->db->table('users')->insertBatch($users);
    }
}
