<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->db->disableForeignKeyChecks();

        foreach (['order_items', 'orders', 'products', 'categories', 'users'] as $table) {
            $this->db->table($table)->truncate();
        }

        $this->db->enableForeignKeyChecks();

        foreach (glob(WRITEPATH . 'uploads/payments/*') ?: [] as $file) {
            if (basename($file) !== 'index.html') {
                unlink($file);
            }
        }

        $this->call(UserSeeder::class);
        $this->call(CatalogSeeder::class);
        $this->call(OrderSeeder::class);
    }
}
