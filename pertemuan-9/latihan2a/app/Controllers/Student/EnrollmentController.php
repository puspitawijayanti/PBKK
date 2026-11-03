<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Services\EnrollmentService;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Academic;
use RuntimeException;

class EnrollmentController extends BaseController
{
    public function index(): string
    {
        return view('student/enrollments/index', $this->pageData('Kartu Rencana Studi (KRS)'));
    }

    public function print(): string
    {
        return view('student/enrollments/print', $this->pageData('Cetak KRS'));
    }

    public function create(): RedirectResponse
    {
        $courseId = (int) $this->request->getPost('course_id');

        try {
            $course = (new EnrollmentService())->enroll($this->currentStudent(), $courseId);
        } catch (RuntimeException $exception) {
            return redirect()->to('student/enrollments')->with('error', $exception->getMessage());
        }

        return redirect()->to('student/enrollments')->with('success', $course['name'] . ' (' . $course['credits'] . ' SKS) ditambahkan ke KRS.');
    }

    public function delete(int $enrollmentId): RedirectResponse
    {
        try {
            $enrollment = (new EnrollmentService())->drop($this->currentStudent(), $enrollmentId);
        } catch (RuntimeException $exception) {
            return redirect()->to('student/enrollments')->with('error', $exception->getMessage());
        }

        return redirect()->to('student/enrollments')->with('success', $enrollment['course_name'] . ' dihapus dari KRS.');
    }

    private function pageData(string $title): array
    {
        $config = config(Academic::class);
        $student = $this->currentStudent();
        $service = new EnrollmentService();
        $selected = model(EnrollmentModel::class)->forPeriod((int) $student['id'], $config->currentAcademicYear, $config->currentTerm);

        return [
            'title'       => $title,
            'config'      => $config,
            'student'     => $student,
            'selected'    => $selected,
            'selectedIds' => array_map('intval', array_column($selected, 'course_id')),
            'offered'     => $service->offeredCourses($student),
            'credits'     => $service->currentCredits((int) $student['id']),
        ];
    }
}
