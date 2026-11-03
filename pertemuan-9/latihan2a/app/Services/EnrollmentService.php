<?php

namespace App\Services;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use Config\Academic;
use RuntimeException;
use Throwable;

class EnrollmentService
{
    private Academic $config;
    private EnrollmentModel $enrollments;

    public function __construct()
    {
        $this->config = config(Academic::class);
        $this->enrollments = model(EnrollmentModel::class);
    }

    public function offeredCourses(array $student): array
    {
        $parity = $this->config->currentTerm === 'odd' ? 1 : 0;

        return model(CourseModel::class)
            ->withDetails()
            ->withEnrolledCount($this->config->currentAcademicYear, $this->config->currentTerm)
            ->where('courses.study_program_id', $student['study_program_id'])
            ->where('MOD(courses.semester, 2) = ' . $parity, null, false)
            ->orderBy('courses.semester')
            ->orderBy('courses.code')
            ->findAll();
    }

    public function currentCredits(int $studentId): int
    {
        $row = db_connect()->table('enrollments')
            ->selectSum('courses.credits', 'credits')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->where('enrollments.student_id', $studentId)
            ->where('enrollments.academic_year', $this->config->currentAcademicYear)
            ->where('enrollments.term', $this->config->currentTerm)
            ->get()
            ->getRow();

        return (int) ($row->credits ?? 0);
    }

    public function enroll(array $student, int $courseId): array
    {
        if (! $this->config->enrollmentOpen) {
            throw new RuntimeException('Masa pengisian KRS sudah ditutup.');
        }

        $year = $this->config->currentAcademicYear;
        $term = $this->config->currentTerm;
        $db = db_connect();
        $db->transBegin();

        try {
            $course = $db->query('SELECT * FROM courses WHERE id = ? FOR UPDATE', [$courseId])->getRowArray();

            if ($course === null) {
                throw new RuntimeException('Mata kuliah tidak ditemukan.');
            }

            if ((int) $course['study_program_id'] !== (int) $student['study_program_id']) {
                throw new RuntimeException('Mata kuliah ' . $course['name'] . ' bukan milik program studi Anda.');
            }

            if ((int) $course['semester'] % 2 !== ($term === 'odd' ? 1 : 0)) {
                throw new RuntimeException('Mata kuliah ' . $course['name'] . ' tidak ditawarkan pada semester ' . term_label($term) . '.');
            }

            $alreadyTaken = $db->table('enrollments')
                ->where('student_id', $student['id'])
                ->where('course_id', $courseId)
                ->where('academic_year', $year)
                ->countAllResults();

            if ($alreadyTaken > 0) {
                throw new RuntimeException('Mata kuliah ' . $course['name'] . ' sudah Anda ambil pada tahun akademik ' . $year . '.');
            }

            $credits = $this->currentCredits((int) $student['id']);

            if ($credits + (int) $course['credits'] > $this->config->maxCredits) {
                throw new RuntimeException('Total SKS akan menjadi ' . ($credits + (int) $course['credits']) . ', melebihi batas ' . $this->config->maxCredits . ' SKS.');
            }

            $enrolled = $db->table('enrollments')
                ->where('course_id', $courseId)
                ->where('academic_year', $year)
                ->where('term', $term)
                ->countAllResults();

            if ($enrolled >= (int) $course['quota']) {
                throw new RuntimeException('Kuota kelas ' . $course['name'] . ' sudah penuh (' . $course['quota'] . ' mahasiswa).');
            }

            $this->enrollments->insert([
                'student_id'    => $student['id'],
                'course_id'     => $courseId,
                'academic_year' => $year,
                'term'          => $term,
            ]);

            $db->transCommit();
        } catch (Throwable $exception) {
            $db->transRollback();

            throw $exception;
        }

        return $course;
    }

    public function drop(array $student, int $enrollmentId): array
    {
        if (! $this->config->enrollmentOpen) {
            throw new RuntimeException('Masa pengisian KRS sudah ditutup.');
        }

        $enrollment = $this->enrollments->withCourse()
            ->where('enrollments.id', $enrollmentId)
            ->where('enrollments.student_id', $student['id'])
            ->first();

        if ($enrollment === null) {
            throw new RuntimeException('Data KRS tidak ditemukan.');
        }

        if ($enrollment['academic_year'] !== $this->config->currentAcademicYear || $enrollment['term'] !== $this->config->currentTerm) {
            throw new RuntimeException('Hanya KRS semester berjalan yang dapat dibatalkan.');
        }

        if ($enrollment['score'] !== null) {
            throw new RuntimeException('Mata kuliah yang sudah dinilai tidak dapat dibatalkan.');
        }

        $this->enrollments->delete($enrollmentId);

        return $enrollment;
    }
}
