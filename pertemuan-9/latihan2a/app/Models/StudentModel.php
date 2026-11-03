<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table         = 'students';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'study_program_id', 'advisor_id', 'student_number', 'entry_year'];
    protected $useTimestamps = true;

    public function withDetails(): static
    {
        return $this->select('students.*, users.name, users.username, study_programs.name AS study_program_name, study_programs.degree, advisor_users.name AS advisor_name')
            ->join('users', 'users.id = students.user_id')
            ->join('study_programs', 'study_programs.id = students.study_program_id')
            ->join('lecturers advisors', 'advisors.id = students.advisor_id', 'left')
            ->join('users advisor_users', 'advisor_users.id = advisors.user_id', 'left');
    }

    public function search(?string $keyword, ?int $studyProgramId, ?int $advisorId = null): static
    {
        $this->withDetails();

        if ($keyword !== null && $keyword !== '') {
            $this->groupStart()->like('users.name', $keyword)->orLike('students.student_number', $keyword)->groupEnd();
        }

        if ($studyProgramId !== null && $studyProgramId > 0) {
            $this->where('students.study_program_id', $studyProgramId);
        }

        if ($advisorId !== null) {
            $this->where('students.advisor_id', $advisorId);
        }

        return $this->orderBy('students.student_number');
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->withDetails()->where('students.user_id', $userId)->first();
    }

    public function findDetailed(int $id): ?array
    {
        return $this->withDetails()->where('students.id', $id)->first();
    }
}
