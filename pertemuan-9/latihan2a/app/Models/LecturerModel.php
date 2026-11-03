<?php

namespace App\Models;

use CodeIgniter\Model;

class LecturerModel extends Model
{
    protected $table         = 'lecturers';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'study_program_id', 'lecturer_number', 'email', 'phone'];
    protected $useTimestamps = true;

    public function withDetails(): static
    {
        return $this->select('lecturers.*, users.name, users.username, study_programs.name AS study_program_name, study_programs.degree')
            ->select('(SELECT COUNT(*) FROM students WHERE students.advisor_id = lecturers.id) AS advisee_count', false)
            ->select('(SELECT COUNT(*) FROM courses WHERE courses.lecturer_id = lecturers.id) AS course_count', false)
            ->join('users', 'users.id = lecturers.user_id')
            ->join('study_programs', 'study_programs.id = lecturers.study_program_id');
    }

    public function search(?string $keyword, ?int $studyProgramId): static
    {
        $this->withDetails();

        if ($keyword !== null && $keyword !== '') {
            $this->groupStart()->like('users.name', $keyword)->orLike('lecturers.lecturer_number', $keyword)->groupEnd();
        }

        if ($studyProgramId !== null && $studyProgramId > 0) {
            $this->where('lecturers.study_program_id', $studyProgramId);
        }

        return $this->orderBy('users.name');
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->withDetails()->where('lecturers.user_id', $userId)->first();
    }

    public function options(): array
    {
        $options = [];

        foreach ($this->withDetails()->orderBy('users.name')->findAll() as $lecturer) {
            $options[$lecturer['id']] = $lecturer['name'];
        }

        return $options;
    }
}
