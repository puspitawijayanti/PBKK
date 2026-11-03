<?php

namespace App\Controllers\Lecturer;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\StudentModel;
use Config\Academic;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $config = config(Academic::class);
        $lecturer = $this->currentLecturer();
        $courses = model(CourseModel::class)
            ->withDetails()
            ->withEnrolledCount($config->currentAcademicYear, $config->currentTerm)
            ->where('courses.lecturer_id', $lecturer['id'])
            ->orderBy('courses.code')
            ->findAll();

        $ungraded = model(EnrollmentModel::class)
            ->join('courses', 'courses.id = enrollments.course_id')
            ->where('courses.lecturer_id', $lecturer['id'])
            ->where('enrollments.academic_year', $config->currentAcademicYear)
            ->where('enrollments.term', $config->currentTerm)
            ->where('enrollments.score', null)
            ->countAllResults();

        return view('lecturer/dashboard', [
            'title'        => 'Dashboard Dosen',
            'config'       => $config,
            'lecturer'     => $lecturer,
            'courses'      => $courses,
            'ungraded'     => $ungraded,
            'adviseeCount' => model(StudentModel::class)->where('advisor_id', $lecturer['id'])->countAllResults(),
        ]);
    }
}
