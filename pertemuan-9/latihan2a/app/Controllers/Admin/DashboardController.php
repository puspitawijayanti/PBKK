<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\LecturerModel;
use App\Models\StudentModel;
use App\Models\StudyProgramModel;
use Config\Academic;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $config = config(Academic::class);

        return view('admin/dashboard', [
            'title'    => 'Dashboard Administrator',
            'config'   => $config,
            'stats'    => [
                'programs'    => model(StudyProgramModel::class)->countAllResults(),
                'lecturers'   => model(LecturerModel::class)->countAllResults(),
                'students'    => model(StudentModel::class)->countAllResults(),
                'courses'     => model(CourseModel::class)->countAllResults(),
                'enrollments' => model(EnrollmentModel::class)
                    ->where('academic_year', $config->currentAcademicYear)
                    ->where('term', $config->currentTerm)
                    ->countAllResults(),
                'noAdvisor'   => model(StudentModel::class)->where('advisor_id', null)->countAllResults(),
                'noLecturer'  => model(CourseModel::class)->where('lecturer_id', null)->countAllResults(),
            ],
            'programs' => model(StudyProgramModel::class)->withCounts()->orderBy('student_count', 'DESC')->findAll(),
        ]);
    }
}
