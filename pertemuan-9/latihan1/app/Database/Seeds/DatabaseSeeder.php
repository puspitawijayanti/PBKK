<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->db->disableForeignKeyChecks();

        foreach (['loans', 'messages', 'tools', 'categories', 'users'] as $table) {
            $this->db->table($table)->truncate();
        }

        $this->db->enableForeignKeyChecks();

        foreach (glob(FCPATH . 'uploads/tools/*') ?: [] as $file) {
            if (basename($file) !== 'index.html') {
                unlink($file);
            }
        }

        $this->call(UserSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(ToolSeeder::class);
        $this->call(LoanSeeder::class);
        $this->call(MessageSeeder::class);
    }
}
