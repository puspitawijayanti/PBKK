<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\LecturerModel;
use App\Models\StudyProgramModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Academic;

class CourseController extends BaseController
{
    private CourseModel $courses;

    public function __construct()
    {
        $this->courses = model(CourseModel::class);
    }

    public function index(): string
    {
        $config = config(Academic::class);
        $keyword = trim((string) $this->request->getGet('q'));
        $programId = (int) $this->request->getGet('program');
        $query = $this->courses->search($keyword, $programId)->withEnrolledCount($config->currentAcademicYear, $config->currentTerm);

        return view('admin/courses/index', [
            'title'     => 'Mata Kuliah',
            'courses'   => $query->paginate(10),
            'pager'     => $query->pager,
            'programs'  => model(StudyProgramModel::class)->options(),
            'keyword'   => $keyword,
            'programId' => $programId,
            'config'    => $config,
        ]);
    }

    public function new(): string
    {
        return view('admin/courses/form', $this->formData('Tambah Mata Kuliah', null));
    }

    public function create(): RedirectResponse
    {
        $data = $this->collectInput();

        if (! $this->courses->insert($data)) {
            return $this->backWithErrors($this->courses->errors());
        }

        return redirect()->to('admin/courses')->with('success', 'Mata kuliah ' . $data['name'] . ' berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        return view('admin/courses/form', $this->formData('Ubah Mata Kuliah', $this->findCourse($id)));
    }

    public function update(int $id): RedirectResponse
    {
        $this->findCourse($id);
        $data = $this->collectInput() + ['id' => $id];

        if (! $this->courses->save($data)) {
            return $this->backWithErrors($this->courses->errors());
        }

        return redirect()->to('admin/courses')->with('success', 'Mata kuliah ' . $data['name'] . ' berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $course = $this->findCourse($id);

        if (model(EnrollmentModel::class)->where('course_id', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Mata kuliah ' . $course['name'] . ' sudah memiliki data KRS sehingga tidak dapat dihapus.');
        }

        $this->courses->delete($id);

        return redirect()->to('admin/courses')->with('success', 'Mata kuliah ' . $course['name'] . ' berhasil dihapus.');
    }

    private function findCourse(int $id): array
    {
        $course = $this->courses->find($id);

        if ($course === null) {
            throw PageNotFoundException::forPageNotFound('Mata kuliah tidak ditemukan.');
        }

        return $course;
    }

    private function formData(string $title, ?array $course): array
    {
        return [
            'title'     => $title,
            'course'    => $course,
            'programs'  => model(StudyProgramModel::class)->options(),
            'lecturers' => model(LecturerModel::class)->options(),
            'config'    => config(Academic::class),
        ];
    }

    private function collectInput(): array
    {
        return [
            'study_program_id' => $this->request->getPost('study_program_id'),
            'lecturer_id'      => $this->request->getPost('lecturer_id') ?: null,
            'code'             => strtoupper(trim((string) $this->request->getPost('code'))),
            'name'             => trim((string) $this->request->getPost('name')),
            'credits'          => $this->request->getPost('credits'),
            'semester'         => $this->request->getPost('semester'),
            'quota'            => $this->request->getPost('quota'),
            'schedule'         => trim((string) $this->request->getPost('schedule')),
            'room'             => trim((string) $this->request->getPost('room')),
        ];
    }
}
