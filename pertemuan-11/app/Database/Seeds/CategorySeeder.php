<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        if ($this->db->table('categories')->countAllResults() > 0) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $rows = [
            ['Elektronik', 'elektronik', 'Gadget dan perangkat elektronik harian.'],
            ['Fashion', 'fashion', 'Pakaian dan alas kaki untuk gaya sehari-hari.'],
            ['Aksesoris', 'aksesoris', 'Pelengkap penampilan dan kebutuhan harian.'],
            ['Rumah Tangga', 'rumah-tangga', 'Perlengkapan rumah dan dapur.'],
        ];

        foreach ($rows as [$name, $slug, $desc]) {
            $this->db->table('categories')->insert([
                'name' => $name, 'slug' => $slug, 'description' => $desc,
                'is_active' => 1, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }
}
