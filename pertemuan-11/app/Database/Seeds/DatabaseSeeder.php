<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Jalankan:  php spark db:seed DatabaseSeeder
 * Aman dijalankan ulang: setiap seeder melewati data yang sudah ada.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call('UserSeeder');
        $this->call('CategorySeeder');
        $this->call('ProductSeeder');
        $this->call('AddressSeeder');
        $this->call('SampleOrderSeeder');
    }
}
