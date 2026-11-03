<?php

namespace App\Database\Seeds;

use App\Services\GradeService;
use CodeIgniter\Database\Seeder;
use Config\Academic;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(2026);

        $config = config(Academic::class);
        $grades = new GradeService();
        $now = date('Y-m-d H:i:s');
        $courses = [];

        foreach ($this->db->table('courses')->get()->getResultArray() as $course) {
            $courses[(int) $course['study_program_id']][(int) $course['semester']][] = $course;
        }

        $students = $this->db->table('students')->orderBy('id')->get()->getResultArray();
        $demoStudentNumber = '5025241001';
        $smallClassFilled = 0;
        $rows = [];

        foreach ($students as $student) {
            $currentSemester = $this->semesterFor((int) $student['entry_year'], $config);
            $skill = mt_rand(60, 92);

            for ($semester = 1; $semester <= $currentSemester; $semester++) {
                [$academicYear, $term] = $this->periodFor((int) $student['entry_year'], $semester);
                $isCurrent = $semester === $currentSemester;

                if ($isCurrent && $student['student_number'] === $demoStudentNumber) {
                    continue;
                }

                foreach ($courses[(int) $student['study_program_id']][$semester] ?? [] as $course) {
                    if ($isCurrent && (int) $course['quota'] < 10) {
                        if ($smallClassFilled >= (int) $course['quota']) {
                            continue;
                        }
                        $smallClassFilled++;
                    }

                    $row = [
                        'student_id'    => $student['id'],
                        'course_id'     => $course['id'],
                        'academic_year' => $academicYear,
                        'term'          => $term,
                        'score'         => null,
                        'grade'         => null,
                        'weight'        => null,
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ];

                    if (! $isCurrent) {
                        $score = max(30, min(100, $skill + mt_rand(-18, 10)));
                        $result = $grades->fromScore($score);
                        $row['score'] = $score;
                        $row['grade'] = $result['grade'];
                        $row['weight'] = $result['weight'];
                    }

                    $rows[] = $row;
                }
            }
        }

        $this->db->table('enrollments')->insertBatch($rows);
    }

    private function semesterFor(int $entryYear, Academic $config): int
    {
        $startYear = (int) substr($config->currentAcademicYear, 0, 4);

        return ($startYear - $entryYear) * 2 + ($config->currentTerm === 'odd' ? 1 : 2);
    }

    private function periodFor(int $entryYear, int $semester): array
    {
        $startYear = $entryYear + intdiv($semester - 1, 2);

        return [$startYear . '/' . ($startYear + 1), $semester % 2 === 1 ? 'odd' : 'even'];
    }
}
