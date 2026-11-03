<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table         = 'courses';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['study_program_id', 'lecturer_id', 'code', 'name', 'credits', 'semester', 'quota', 'schedule', 'room'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'               => 'permit_empty|is_natural_no_zero',
        'study_program_id' => 'required|is_natural_no_zero|is_not_unique[study_programs.id]',
        'lecturer_id'      => 'permit_empty|is_natural_no_zero|is_not_unique[lecturers.id]',
        'code'             => 'required|alpha_numeric|max_length[15]|is_unique[courses.code,id,{id}]',
        'name'             => 'required|min_length[3]|max_length[120]',
        'credits'          => 'required|is_natural_no_zero|less_than_equal_to[6]',
        'semester'         => 'required|is_natural_no_zero|less_than_equal_to[8]',
        'quota'            => 'required|is_natural_no_zero|less_than_equal_to[200]',
        'schedule'         => 'permit_empty|max_length[60]',
        'room'             => 'permit_empty|max_length[30]',
    ];

    protected $validationMessages = [
        'study_program_id' => [
            'required'           => 'Program studi wajib dipilih.',
            'is_natural_no_zero' => 'Program studi wajib dipilih.',
            'is_not_unique'      => 'Program studi tidak ditemukan.',
        ],
        'lecturer_id' => [
            'is_not_unique' => 'Dosen pengampu tidak ditemukan.',
        ],
        'code' => [
            'required'      => 'Kode mata kuliah wajib diisi.',
            'alpha_numeric' => 'Kode mata kuliah hanya boleh huruf dan angka.',
            'is_unique'     => 'Kode mata kuliah sudah dipakai.',
        ],
        'name' => [
            'required'   => 'Nama mata kuliah wajib diisi.',
            'min_length' => 'Nama mata kuliah minimal 3 karakter.',
        ],
        'credits' => [
            'required'           => 'SKS wajib diisi.',
            'is_natural_no_zero' => 'SKS minimal 1.',
            'less_than_equal_to' => 'SKS maksimal 6.',
        ],
        'semester' => [
            'required'           => 'Semester wajib diisi.',
            'is_natural_no_zero' => 'Semester minimal 1.',
            'less_than_equal_to' => 'Semester maksimal 8.',
        ],
        'quota' => [
            'required'           => 'Kuota wajib diisi.',
            'is_natural_no_zero' => 'Kuota minimal 1.',
            'less_than_equal_to' => 'Kuota maksimal 200.',
        ],
    ];

    public function withDetails(): static
    {
        return $this->select('courses.*, study_programs.name AS study_program_name, users.name AS lecturer_name')
            ->join('study_programs', 'study_programs.id = courses.study_program_id')
            ->join('lecturers', 'lecturers.id = courses.lecturer_id', 'left')
            ->join('users', 'users.id = lecturers.user_id', 'left');
    }

    public function withEnrolledCount(string $academicYear, string $term): static
    {
        return $this->select(
            '(SELECT COUNT(*) FROM enrollments WHERE enrollments.course_id = courses.id AND enrollments.academic_year = ' . $this->db->escape($academicYear) . ' AND enrollments.term = ' . $this->db->escape($term) . ') AS enrolled_count',
            false
        );
    }

    public function search(?string $keyword, ?int $studyProgramId): static
    {
        $this->withDetails();

        if ($keyword !== null && $keyword !== '') {
            $this->groupStart()->like('courses.name', $keyword)->orLike('courses.code', $keyword)->groupEnd();
        }

        if ($studyProgramId !== null && $studyProgramId > 0) {
            $this->where('courses.study_program_id', $studyProgramId);
        }

        return $this->orderBy('courses.semester')->orderBy('courses.code');
    }
}
