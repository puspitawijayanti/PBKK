<?php

namespace App\Models;

use CodeIgniter\Model;

class StudyProgramModel extends Model
{
    public const DEGREES = ['D3', 'D4', 'S1', 'S2'];

    protected $table         = 'study_programs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['code', 'name', 'degree'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'     => 'permit_empty|is_natural_no_zero',
        'code'   => 'required|alpha_numeric|max_length[10]|is_unique[study_programs.code,id,{id}]',
        'name'   => 'required|min_length[3]|max_length[100]',
        'degree' => 'required|in_list[D3,D4,S1,S2]',
    ];

    protected $validationMessages = [
        'code' => [
            'required'      => 'Kode prodi wajib diisi.',
            'alpha_numeric' => 'Kode prodi hanya boleh huruf dan angka.',
            'max_length'    => 'Kode prodi maksimal 10 karakter.',
            'is_unique'     => 'Kode prodi sudah dipakai.',
        ],
        'name' => [
            'required'   => 'Nama prodi wajib diisi.',
            'min_length' => 'Nama prodi minimal 3 karakter.',
            'max_length' => 'Nama prodi maksimal 100 karakter.',
        ],
        'degree' => [
            'required' => 'Jenjang wajib dipilih.',
            'in_list'  => 'Jenjang tidak valid.',
        ],
    ];

    public function withCounts(): static
    {
        return $this->select('study_programs.*')
            ->select('(SELECT COUNT(*) FROM students WHERE students.study_program_id = study_programs.id) AS student_count', false)
            ->select('(SELECT COUNT(*) FROM lecturers WHERE lecturers.study_program_id = study_programs.id) AS lecturer_count', false)
            ->select('(SELECT COUNT(*) FROM courses WHERE courses.study_program_id = study_programs.id) AS course_count', false);
    }

    public function options(): array
    {
        $options = [];

        foreach ($this->orderBy('name')->findAll() as $program) {
            $options[$program['id']] = $program['degree'] . ' ' . $program['name'];
        }

        return $options;
    }
}
