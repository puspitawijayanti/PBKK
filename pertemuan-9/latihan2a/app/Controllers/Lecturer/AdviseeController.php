<?php

namespace App\Controllers\Lecturer;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\StudentModel;
use App\Services\EnrollmentService;
use App\Services\GradeService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class AdviseeController extends BaseController
{
    public function index(): string
    {
        $lecturer = $this->currentLecturer();
        $grades = new GradeService();
        $enrollmentService = new EnrollmentService();
        $enrollments = model(EnrollmentModel::class);
        $students = model(StudentModel::class)->search(null, null, (int) $lecturer['id'])->findAll();

        foreach ($students as &$student) {
            $student['summary'] = $grades->summarize($enrollments->allForStudent((int) $student['id']));
            $student['current_credits'] = $enrollmentService->currentCredits((int) $student['id']);
        }

        return view('lecturer/advisees/index', [
            'title'    => 'Mahasiswa Perwalian',
            'students' => $students,
        ]);
    }

    public function show(int $studentId): ResponseInterface|string
    {
        $student = model(StudentModel::class)->findDetailed($studentId);

        if ($student === null) {
            throw PageNotFoundException::forPageNotFound('Mahasiswa tidak ditemukan.');
        }

        if ((int) $student['advisor_id'] !== (int) $this->currentLecturer()['id']) {
            return $this->forbidden('Mahasiswa ' . $student['name'] . ' bukan mahasiswa perwalian Anda.');
        }

        $grades = new GradeService();
        $all = model(EnrollmentModel::class)->allForStudent($studentId);
        $periods = [];

        foreach ($all as $enrollment) {
            $key = $enrollment['academic_year'] . '|' . $enrollment['term'];
            $periods[$key]['label'] = period_label($enrollment['academic_year'], $enrollment['term']);
            $periods[$key]['rows'][] = $enrollment;
        }

        foreach ($periods as &$period) {
            $period['summary'] = $grades->summarize($period['rows']);
        }

        return view('lecturer/advisees/show', [
            'title'   => 'Transkrip ' . $student['name'],
            'student' => $student,
            'periods' => array_reverse($periods),
            'summary' => $grades->summarize($all),
        ]);
    }
}
