<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->db->disableForeignKeyChecks();

        foreach (['enrollments', 'courses', 'students', 'lecturers', 'study_programs', 'users'] as $table) {
            $this->db->table($table)->truncate();
        }

        $this->db->enableForeignKeyChecks();

        $this->call(StudyProgramSeeder::class);
        $this->call(LecturerSeeder::class);
        $this->call(StudentSeeder::class);
        $this->call(CourseSeeder::class);
        $this->call(EnrollmentSeeder::class);
    }
}
