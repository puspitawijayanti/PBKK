<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Services\EnrollmentService;
use App\Services\GradeService;
use Config\Academic;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $config = config(Academic::class);
        $student = $this->currentStudent();
        $enrollments = model(EnrollmentModel::class);

        return view('student/dashboard', [
            'title'          => 'Dashboard Mahasiswa',
            'config'         => $config,
            'student'        => $student,
            'summary'        => (new GradeService())->summarize($enrollments->allForStudent((int) $student['id'])),
            'currentCredits' => (new EnrollmentService())->currentCredits((int) $student['id']),
            'current'        => $enrollments->forPeriod((int) $student['id'], $config->currentAcademicYear, $config->currentTerm),
        ]);
    }
}
