<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\LecturerModel;
use App\Models\StudyProgramModel;
use App\Models\UserModel;
use App\Services\AccountService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class LecturerController extends BaseController
{
    private LecturerModel $lecturers;

    public function __construct()
    {
        $this->lecturers = model(LecturerModel::class);
    }

    public function index(): string
    {
        $keyword = trim((string) $this->request->getGet('q'));
        $programId = (int) $this->request->getGet('program');
        $query = $this->lecturers->search($keyword, $programId);

        return view('admin/lecturers/index', [
            'title'     => 'Data Dosen',
            'lecturers' => $query->paginate(10),
            'pager'     => $query->pager,
            'programs'  => model(StudyProgramModel::class)->options(),
            'keyword'   => $keyword,
            'programId' => $programId,
        ]);
    }

    public function new(): string
    {
        return view('admin/lecturers/form', [
            'title'    => 'Tambah Dosen',
            'lecturer' => null,
            'programs' => model(StudyProgramModel::class)->options(),
        ]);
    }

    public function create(): RedirectResponse
    {
        if (! $this->validate($this->rules(null))) {
            return $this->backWithErrors($this->validator->getErrors());
        }

        $data = $this->validator->getValidated();
        (new AccountService())->createLecturer($data);

        return redirect()->to('admin/lecturers')->with('success', 'Dosen ' . $data['name'] . ' ditambahkan. Akun login: ' . $data['lecturer_number'] . ' dengan password awal sama dengan NIDN.');
    }

    public function edit(int $id): string
    {
        return view('admin/lecturers/form', [
            'title'    => 'Ubah Dosen',
            'lecturer' => $this->findLecturer($id),
            'programs' => model(StudyProgramModel::class)->options(),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $lecturer = $this->findLecturer($id);

        if (! $this->validate($this->rules($lecturer))) {
            return $this->backWithErrors($this->validator->getErrors());
        }

        $data = $this->validator->getValidated();
        (new AccountService())->updateLecturer($lecturer, $data);

        return redirect()->to('admin/lecturers')->with('success', 'Data dosen ' . $data['name'] . ' berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $lecturer = $this->findLecturer($id);

        if ((int) $lecturer['course_count'] > 0) {
            return redirect()->back()->with('error', 'Dosen ' . $lecturer['name'] . ' masih mengampu ' . $lecturer['course_count'] . ' mata kuliah. Ganti pengampu terlebih dahulu.');
        }

        model(UserModel::class)->delete($lecturer['user_id']);

        return redirect()->to('admin/lecturers')->with('success', 'Dosen ' . $lecturer['name'] . ' dihapus. Mahasiswa perwaliannya kini tanpa dosen wali.');
    }

    private function findLecturer(int $id): array
    {
        $lecturer = $this->lecturers->withDetails()->where('lecturers.id', $id)->first();

        if ($lecturer === null) {
            throw PageNotFoundException::forPageNotFound('Dosen tidak ditemukan.');
        }

        return $lecturer;
    }

    private function rules(?array $lecturer): array
    {
        $lecturerId = $lecturer['id'] ?? 0;
        $userId = $lecturer['user_id'] ?? 0;

        return [
            'name' => [
                'rules'  => 'required|min_length[3]|max_length[100]',
                'errors' => ['required' => 'Nama dosen wajib diisi.', 'min_length' => 'Nama minimal 3 karakter.', 'max_length' => 'Nama maksimal 100 karakter.'],
            ],
            'lecturer_number' => [
                'rules'  => 'required|numeric|min_length[8]|max_length[20]|is_unique[lecturers.lecturer_number,id,' . $lecturerId . ']|is_unique[users.username,id,' . $userId . ']',
                'errors' => [
                    'required'   => 'NIDN wajib diisi.',
                    'numeric'    => 'NIDN hanya boleh berisi angka.',
                    'min_length' => 'NIDN minimal 8 digit.',
                    'max_length' => 'NIDN maksimal 20 digit.',
                    'is_unique'  => 'NIDN sudah terdaftar atau sudah dipakai sebagai username.',
                ],
            ],
            'study_program_id' => [
                'rules'  => 'required|is_not_unique[study_programs.id]',
                'errors' => ['required' => 'Program studi wajib dipilih.', 'is_not_unique' => 'Program studi tidak ditemukan.'],
            ],
            'email' => [
                'rules'  => 'permit_empty|valid_email|max_length[120]',
                'errors' => ['valid_email' => 'Format email tidak valid.'],
            ],
            'phone' => [
                'rules'  => 'permit_empty|regex_match[/^[0-9+\-\s]{8,20}$/]',
                'errors' => ['regex_match' => 'Nomor telepon tidak valid.'],
            ],
        ];
    }
}
