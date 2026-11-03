<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $buyerPassword = password_hash('pembeli123', PASSWORD_DEFAULT);

        $rows = [[
            'name'       => 'Admin Kopi Nusantara',
            'email'      => 'admin@kopinusantara.id',
            'phone'      => '081234567890',
            'address'    => 'Jl. Kopi Tubruk No. 12, Surabaya',
            'password'   => password_hash('admin123', PASSWORD_DEFAULT),
            'role'       => 'admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]];

        $buyers = [
            ['Rudi Hartono', 'rudi@contoh.id', '081211110001', 'Jl. Darmo Permai III No. 8, Surabaya 60226'],
            ['Sinta Maharani', 'sinta@contoh.id', '081211110002', 'Jl. Kaliurang Km 5 No. 21, Sleman 55281'],
            ['Agung Wibowo', 'agung@contoh.id', '081211110003', 'Jl. Dago No. 115, Bandung 40135'],
            ['Melati Kusuma', 'melati@contoh.id', '081211110004', 'Jl. Panglima Polim No. 9, Jakarta Selatan 12160'],
            ['Bayu Prasetya', 'bayu@contoh.id', '081211110005', 'Jl. Ijen No. 40, Malang 65119'],
            ['Nadia Larasati', 'nadia@contoh.id', '081211110006', 'Jl. Pemuda No. 77, Semarang 50132'],
            ['Fajar Siddiq', 'fajar@contoh.id', '081211110007', 'Jl. Teuku Umar No. 3, Denpasar 80113'],
            ['Oktaviani Putri', 'okta@contoh.id', '081211110008', 'Jl. Sudirman No. 18, Pekanbaru 28116'],
            ['Hendra Gunawan', 'hendra@contoh.id', '081211110009', 'Jl. Pettarani No. 55, Makassar 90222'],
            ['Wulan Sari', 'wulan@contoh.id', '081211110010', 'Jl. Gatot Subroto No. 31, Medan 20112'],
        ];

        foreach ($buyers as [$name, $email, $phone, $address]) {
            $rows[] = [
                'name'       => $name,
                'email'      => $email,
                'phone'      => $phone,
                'address'    => $address,
                'password'   => $buyerPassword,
                'role'       => 'buyer',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $this->db->table('users')->insertBatch($rows);
    }
}
