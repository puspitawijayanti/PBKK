<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\LecturerModel;
use App\Models\StudentModel;
use App\Models\StudyProgramModel;
use App\Models\UserModel;
use App\Services\AccountService;
use App\Services\GradeService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class StudentController extends BaseController
{
    private StudentModel $students;

    public function __construct()
    {
        $this->students = model(StudentModel::class);
    }

    public function index(): string
    {
        $keyword = trim((string) $this->request->getGet('q'));
        $programId = (int) $this->request->getGet('program');
        $query = $this->students->search($keyword, $programId);

        return view('admin/students/index', [
            'title'     => 'Data Mahasiswa',
            'students'  => $query->paginate(10),
            'pager'     => $query->pager,
            'programs'  => model(StudyProgramModel::class)->options(),
            'keyword'   => $keyword,
            'programId' => $programId,
        ]);
    }

    public function new(): string
    {
        return view('admin/students/form', $this->formData('Tambah Mahasiswa', null));
    }

    public function create(): RedirectResponse
    {
        if (! $this->validate($this->rules(null))) {
            return $this->backWithErrors($this->validator->getErrors());
        }

        $data = $this->validator->getValidated();
        (new AccountService())->createStudent($data);

        return redirect()->to('admin/students')->with('success', 'Mahasiswa ' . $data['name'] . ' ditambahkan. Akun login dibuat otomatis: username dan password awal = ' . $data['student_number'] . '.');
    }

    public function edit(int $id): string
    {
        $student = $this->findStudent($id);
        $summary = (new GradeService())->summarize(model(EnrollmentModel::class)->allForStudent($id));

        return view('admin/students/form', $this->formData('Ubah Mahasiswa', $student) + ['summary' => $summary]);
    }

    public function update(int $id): RedirectResponse
    {
        $student = $this->findStudent($id);

        if (! $this->validate($this->rules($student))) {
            return $this->backWithErrors($this->validator->getErrors());
        }

        $data = $this->validator->getValidated();
        (new AccountService())->updateStudent($student, $data);

        return redirect()->to('admin/students')->with('success', 'Data mahasiswa ' . $data['name'] . ' berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $student = $this->findStudent($id);
        model(UserModel::class)->delete($student['user_id']);

        return redirect()->to('admin/students')->with('success', 'Mahasiswa ' . $student['name'] . ' beserta akun dan KRS-nya berhasil dihapus.');
    }

    private function findStudent(int $id): array
    {
        $student = $this->students->findDetailed($id);

        if ($student === null) {
            throw PageNotFoundException::forPageNotFound('Mahasiswa tidak ditemukan.');
        }

        return $student;
    }

    private function formData(string $title, ?array $student): array
    {
        return [
            'title'     => $title,
            'student'   => $student,
            'programs'  => model(StudyProgramModel::class)->options(),
            'lecturers' => model(LecturerModel::class)->options(),
        ];
    }

    private function rules(?array $student): array
    {
        $studentId = $student['id'] ?? 0;
        $userId = $student['user_id'] ?? 0;
        $maxYear = (int) date('Y') + 1;

        return [
            'name' => [
                'rules'  => 'required|min_length[3]|max_length[100]',
                'errors' => ['required' => 'Nama mahasiswa wajib diisi.', 'min_length' => 'Nama minimal 3 karakter.', 'max_length' => 'Nama maksimal 100 karakter.'],
            ],
            'student_number' => [
                'rules'  => 'required|numeric|min_length[5]|max_length[20]|is_unique[students.student_number,id,' . $studentId . ']|is_unique[users.username,id,' . $userId . ']',
                'errors' => [
                    'required'   => 'NIM wajib diisi.',
                    'numeric'    => 'NIM hanya boleh berisi angka.',
                    'min_length' => 'NIM minimal 5 digit.',
                    'max_length' => 'NIM maksimal 20 digit.',
                    'is_unique'  => 'NIM sudah terdaftar atau sudah dipakai sebagai username.',
                ],
            ],
            'study_program_id' => [
                'rules'  => 'required|is_not_unique[study_programs.id]',
                'errors' => ['required' => 'Program studi wajib dipilih.', 'is_not_unique' => 'Program studi tidak ditemukan.'],
            ],
            'advisor_id' => [
                'rules'  => 'permit_empty|is_not_unique[lecturers.id]',
                'errors' => ['is_not_unique' => 'Dosen wali tidak ditemukan.'],
            ],
            'entry_year' => [
                'rules'  => 'required|integer|greater_than_equal_to[2000]|less_than_equal_to[' . $maxYear . ']',
                'errors' => [
                    'required'              => 'Tahun masuk wajib diisi.',
                    'integer'               => 'Tahun masuk harus berupa angka.',
                    'greater_than_equal_to' => 'Tahun masuk minimal 2000.',
                    'less_than_equal_to'    => 'Tahun masuk maksimal ' . $maxYear . '.',
                ],
            ],
        ];
    }
}
