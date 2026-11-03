<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $password = password_hash('mahasiswa123', PASSWORD_DEFAULT);
        $programIds = array_column($this->db->table('study_programs')->get()->getResultArray(), 'id', 'code');
        $advisors = [];

        foreach ($this->db->table('lecturers')->orderBy('id')->get()->getResultArray() as $lecturer) {
            $advisors[(int) $lecturer['study_program_id']][] = (int) $lecturer['id'];
        }

        $groups = [
            ['IF', 2024, '50252410', ['Aditya Nugraha', 'Bella Safitri', 'Christian Hutapea', 'Dinda Maharani', 'Erlangga Putra', 'Farah Azzahra', 'Galang Ramadhan', 'Hana Pertiwi', 'Irfan Hakim', 'Jasmine Aulia']],
            ['IF', 2025, '50252510', ['Kevin Santoso', 'Laila Fitriani', 'Muhammad Rizki', 'Nabila Khairunnisa', 'Oscar Wijaya', 'Putri Ayuningtyas', 'Raka Mahendra', 'Salsabila Putri', 'Teguh Prakoso', 'Vania Anggraeni']],
            ['SI', 2024, '50262410', ['Wahyu Saputra', 'Yasmin Amalia', 'Zaki Firmansyah', 'Anisa Rahmawati', 'Bagas Pratama']],
            ['SI', 2025, '50262510', ['Citra Melati', 'Dimas Aryo', 'Elsa Novita', 'Fikri Hidayat', 'Gilang Saputra']],
        ];

        $counter = 0;

        foreach ($groups as [$programCode, $entryYear, $prefix, $names]) {
            $programId = $programIds[$programCode];
            $programAdvisors = $advisors[$programId];

            foreach ($names as $index => $name) {
                $studentNumber = $prefix . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
                $advisorId = $counter % 11 === 10 ? null : $programAdvisors[$counter % count($programAdvisors)];
                $counter++;

                $this->db->table('users')->insert([
                    'name'       => $name,
                    'username'   => $studentNumber,
                    'password'   => $password,
                    'role'       => 'student',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $this->db->table('students')->insert([
                    'user_id'          => $this->db->insertID(),
                    'study_program_id' => $programId,
                    'advisor_id'       => $advisorId,
                    'student_number'   => $studentNumber,
                    'entry_year'       => $entryYear,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
            }
        }
    }
}
