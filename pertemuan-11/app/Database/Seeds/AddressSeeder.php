<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/** Alamat contoh (fiktif). */
class AddressSeeder extends Seeder
{
    public function run(): void
    {
        if ($this->db->table('addresses')->countAllResults() > 0) {
            return;
        }

        $users = [];
        foreach ($this->db->table('users')->where('role', 'customer')->orderBy('id')->get()->getResultArray() as $u) {
            $users[$u['email']] = $u;
        }
        $now = date('Y-m-d H:i:s');

        $rows = [
            ['customer1@shopease.test', 'Budi Santoso', '081200000011', 'Jl. Melati No. 12, RT 03/RW 05, Kebayoran Baru', 'Jakarta Selatan', 'DKI Jakarta', '12120', 1],
            ['customer1@shopease.test', 'Budi Santoso (Kantor)', '081200000011', 'Gedung Contoh Lt. 5, Jl. Sudirman Kav. 10', 'Jakarta Pusat', 'DKI Jakarta', '10220', 0],
            ['customer2@shopease.test', 'Siti Rahma', '081200000012', 'Jl. Kenanga No. 7, Setiabudi', 'Bandung', 'Jawa Barat', '40391', 1],
        ];

        foreach ($rows as [$email, $name, $phone, $addr, $city, $prov, $zip, $default]) {
            $this->db->table('addresses')->insert([
                'user_id' => $users[$email]['id'], 'recipient_name' => $name, 'phone' => $phone,
                'address' => $addr, 'city' => $city, 'province' => $prov, 'postal_code' => $zip,
                'is_default' => $default, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }
}
