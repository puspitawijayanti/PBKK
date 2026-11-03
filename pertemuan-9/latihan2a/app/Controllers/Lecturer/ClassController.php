<?php

namespace App\Controllers\Lecturer;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Services\GradeService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Academic;

class ClassController extends BaseController
{
    public function index(): string
    {
        $config = config(Academic::class);
        $lecturer = $this->currentLecturer();

        return view('lecturer/classes/index', [
            'title'   => 'Kelas yang Diampu',
            'config'  => $config,
            'courses' => model(CourseModel::class)
                ->withDetails()
                ->withEnrolledCount($config->currentAcademicYear, $config->currentTerm)
                ->where('courses.lecturer_id', $lecturer['id'])
                ->orderBy('courses.semester')
                ->orderBy('courses.code')
                ->findAll(),
        ]);
    }

    public function show(int $courseId): ResponseInterface|string
    {
        $course = $this->findCourse($courseId);

        if (! $this->ownsCourse($course)) {
            return $this->forbidden('Anda bukan dosen pengampu mata kuliah ' . $course['name'] . '.');
        }

        $config = config(Academic::class);
        $periods = db_connect()->table('enrollments')
            ->select('academic_year, term')
            ->where('course_id', $courseId)
            ->groupBy(['academic_year', 'term'])
            ->orderBy('academic_year', 'DESC')
            ->orderBy('term')
            ->get()
            ->getResultArray();

        $year = (string) ($this->request->getGet('year') ?? $config->currentAcademicYear);
        $term = (string) ($this->request->getGet('term') ?? $config->currentTerm);

        return view('lecturer/classes/show', [
            'title'     => $course['code'] . ' ' . $course['name'],
            'config'    => $config,
            'course'    => $course,
            'periods'   => $periods,
            'year'      => $year,
            'term'      => $term,
            'isCurrent' => $year === $config->currentAcademicYear && $term === $config->currentTerm,
            'roster'    => model(EnrollmentModel::class)->roster($courseId, $year, $term),
        ]);
    }

    public function saveScores(int $courseId): ResponseInterface
    {
        $course = $this->findCourse($courseId);

        if (! $this->ownsCourse($course)) {
            return $this->forbidden('Anda tidak berhak mengisi nilai mata kuliah ' . $course['name'] . '.');
        }

        $scores = (array) $this->request->getPost('scores');
        $errors = [];

        foreach ($scores as $enrollmentId => $score) {
            $score = trim((string) $score);

            if ($score !== '' && (! is_numeric($score) || (float) $score < 0 || (float) $score > 100)) {
                $errors['score_' . $enrollmentId] = 'Nilai harus berupa angka 0 sampai 100.';
            }
        }

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $grades = new GradeService();
        $updated = 0;

        $db = db_connect();
        $db->transStart();

        foreach ($scores as $enrollmentId => $score) {
            $score = trim((string) $score);
            $data = ['score' => null, 'grade' => null, 'weight' => null];

            if ($score !== '') {
                $result = $grades->fromScore((float) $score);
                $data = ['score' => round((float) $score, 2), 'grade' => $result['grade'], 'weight' => $result['weight']];
            }

            $db->table('enrollments')
                ->where('id', (int) $enrollmentId)
                ->where('course_id', $courseId)
                ->update($data + ['updated_at' => date('Y-m-d H:i:s')]);
            $updated += $db->affectedRows();
        }

        $db->transComplete();

        return redirect()->back()->with('success', $updated . ' nilai mahasiswa berhasil disimpan. Huruf mutu dan bobot dihitung otomatis.');
    }

    private function findCourse(int $courseId): array
    {
        $course = model(CourseModel::class)->withDetails()->where('courses.id', $courseId)->first();

        if ($course === null) {
            throw PageNotFoundException::forPageNotFound('Mata kuliah tidak ditemukan.');
        }

        return $course;
    }

    private function ownsCourse(array $course): bool
    {
        return (int) $course['lecturer_id'] === (int) $this->currentLecturer()['id'];
    }
}
