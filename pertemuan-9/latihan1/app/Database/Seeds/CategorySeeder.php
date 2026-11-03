<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $names = [
            'Alat Ukur Listrik',
            'Mikrokontroler',
            'Sensor dan Modul',
            'Jaringan Komputer',
            'Catu Daya',
            'Perkakas Tangan',
            'Multimedia',
            'Robotika',
            'Optik dan Fisika',
            'Keselamatan Kerja',
        ];

        $rows = array_map(static fn (string $name): array => ['name' => $name, 'created_at' => $now, 'updated_at' => $now], $names);

        $this->db->table('categories')->insertBatch($rows);
    }
}
