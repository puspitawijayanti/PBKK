<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table         = 'enrollments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['student_id', 'course_id', 'academic_year', 'term', 'score', 'grade', 'weight'];
    protected $useTimestamps = true;

    public function withCourse(): static
    {
        return $this->select('enrollments.*, courses.code, courses.name AS course_name, courses.credits, courses.semester, courses.schedule, courses.room, users.name AS lecturer_name')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->join('lecturers', 'lecturers.id = courses.lecturer_id', 'left')
            ->join('users', 'users.id = lecturers.user_id', 'left');
    }

    public function forPeriod(int $studentId, string $academicYear, string $term): array
    {
        return $this->withCourse()
            ->where('enrollments.student_id', $studentId)
            ->where('enrollments.academic_year', $academicYear)
            ->where('enrollments.term', $term)
            ->orderBy('courses.code')
            ->findAll();
    }

    public function allForStudent(int $studentId): array
    {
        return $this->withCourse()
            ->where('enrollments.student_id', $studentId)
            ->orderBy('enrollments.academic_year')
            ->orderBy('enrollments.term', 'DESC')
            ->orderBy('courses.code')
            ->findAll();
    }

    public function periodsForStudent(int $studentId): array
    {
        return $this->db->table($this->table)
            ->select('academic_year, term')
            ->where('student_id', $studentId)
            ->groupBy(['academic_year', 'term'])
            ->orderBy('academic_year', 'DESC')
            ->orderBy('term')
            ->get()
            ->getResultArray();
    }

    public function roster(int $courseId, string $academicYear, string $term): array
    {
        return $this->select('enrollments.*, students.student_number, users.name AS student_name')
            ->join('students', 'students.id = enrollments.student_id')
            ->join('users', 'users.id = students.user_id')
            ->where('enrollments.course_id', $courseId)
            ->where('enrollments.academic_year', $academicYear)
            ->where('enrollments.term', $term)
            ->orderBy('students.student_number')
            ->findAll();
    }
}
