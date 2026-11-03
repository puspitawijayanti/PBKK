<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $programIds = array_column($this->db->table('study_programs')->get()->getResultArray(), 'id', 'code');
        $lecturers = [];

        foreach ($this->db->table('lecturers')->orderBy('id')->get()->getResultArray() as $lecturer) {
            $lecturers[(int) $lecturer['study_program_id']][] = (int) $lecturer['id'];
        }

        $curriculum = [
            'IF' => [
                1 => [['Dasar Pemrograman', 4], ['Matematika Diskrit', 3], ['Kalkulus I', 3], ['Pengantar Teknologi Informasi', 2]],
                2 => [['Struktur Data', 4], ['Aljabar Linear', 3], ['Organisasi Komputer', 3], ['Statistika', 3]],
                3 => [['Basis Data', 4], ['Pemrograman Berorientasi Objek', 4], ['Sistem Operasi', 3], ['Teori Graf', 3]],
                4 => [['Jaringan Komputer', 4], ['Rekayasa Perangkat Lunak', 3], ['Analisis dan Desain Algoritma', 3], ['Interaksi Manusia dan Komputer', 3]],
                5 => [['Pemrograman Berbasis Kerangka Kerja', 4], ['Kecerdasan Buatan', 3], ['Pemrograman Web', 3], ['Keamanan Informasi', 3]],
                6 => [['Pembelajaran Mesin', 3], ['Komputasi Awan', 3], ['Manajemen Proyek TI', 3], ['Kerja Praktik', 2]],
            ],
            'SI' => [
                1 => [['Pengantar Sistem Informasi', 3], ['Algoritma dan Pemrograman', 4], ['Manajemen Umum', 3]],
                2 => [['Analisis Proses Bisnis', 3], ['Basis Data Terapan', 4], ['Akuntansi Dasar', 3]],
                3 => [['Analisis dan Perancangan Sistem', 4], ['Sistem Enterprise', 3], ['Statistika Bisnis', 3]],
                4 => [['Tata Kelola TI', 3], ['E-Business', 3], ['Gudang Data', 3]],
            ],
        ];

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $slots = ['07.00-09.30', '10.00-12.30', '13.00-15.30'];
        $rows = [];

        foreach ($curriculum as $programCode => $semesters) {
            $programId = $programIds[$programCode];
            $programLecturers = $lecturers[$programId];
            $index = 0;

            foreach ($semesters as $semester => $courses) {
                foreach ($courses as $position => [$name, $credits]) {
                    $isSmallClass = $name === 'Kecerdasan Buatan';
                    $rows[] = [
                        'study_program_id' => $programId,
                        'lecturer_id'      => $programLecturers[($position + 2 * ($semester - 1)) % count($programLecturers)],
                        'code'             => $programCode . $semester . str_pad((string) ($position + 1), 2, '0', STR_PAD_LEFT),
                        'name'             => $name,
                        'credits'          => $credits,
                        'semester'         => $semester,
                        'quota'            => $isSmallClass ? 3 : 40,
                        'schedule'         => $days[$index % 5] . ' ' . $slots[intdiv($index, 5) % 3],
                        'room'             => $programCode . '-' . (101 + $index % 6),
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ];
                    $index++;
                }
            }
        }

        $this->db->table('courses')->insertBatch($rows);
    }
}
