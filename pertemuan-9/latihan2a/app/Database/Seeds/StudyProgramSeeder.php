<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StudyProgramSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $programs = [
            ['IF', 'Teknik Informatika', 'S1'],
            ['SI', 'Sistem Informasi', 'S1'],
            ['TE', 'Teknik Elektro', 'S1'],
            ['TK', 'Teknik Komputer', 'S1'],
            ['MA', 'Matematika', 'S1'],
            ['ST', 'Statistika', 'S1'],
            ['FI', 'Fisika', 'S1'],
            ['TI', 'Teknik Industri', 'S1'],
            ['DKV', 'Desain Komunikasi Visual', 'S1'],
            ['TID3', 'Teknologi Informasi', 'D3'],
        ];

        $rows = [];

        foreach ($programs as [$code, $name, $degree]) {
            $rows[] = ['code' => $code, 'name' => $name, 'degree' => $degree, 'created_at' => $now, 'updated_at' => $now];
        }

        $this->db->table('study_programs')->insertBatch($rows);

        $this->db->table('users')->insert([
            'name'       => 'Administrator Akademik',
            'username'   => 'admin',
            'password'   => password_hash('admin123', PASSWORD_DEFAULT),
            'role'       => 'admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
