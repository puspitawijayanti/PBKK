<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StudyProgramModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class StudyProgramController extends BaseController
{
    private StudyProgramModel $programs;

    public function __construct()
    {
        $this->programs = model(StudyProgramModel::class);
    }

    public function index(): string
    {
        $query = $this->programs->withCounts()->orderBy('code');

        return view('admin/study_programs/index', [
            'title'    => 'Program Studi',
            'programs' => $query->paginate(10),
            'pager'    => $query->pager,
        ]);
    }

    public function new(): string
    {
        return view('admin/study_programs/form', ['title' => 'Tambah Program Studi', 'program' => null]);
    }

    public function create(): RedirectResponse
    {
        $data = $this->collectInput();

        if (! $this->programs->insert($data)) {
            return $this->backWithErrors($this->programs->errors());
        }

        return redirect()->to('admin/study-programs')->with('success', 'Program studi ' . $data['name'] . ' berhasil ditambahkan.');
    }

    public function edit(int $id): string
    {
        return view('admin/study_programs/form', ['title' => 'Ubah Program Studi', 'program' => $this->findProgram($id)]);
    }

    public function update(int $id): RedirectResponse
    {
        $this->findProgram($id);
        $data = $this->collectInput() + ['id' => $id];

        if (! $this->programs->save($data)) {
            return $this->backWithErrors($this->programs->errors());
        }

        return redirect()->to('admin/study-programs')->with('success', 'Program studi berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $program = $this->programs->withCounts()->where('study_programs.id', $id)->first();

        if ($program === null) {
            throw PageNotFoundException::forPageNotFound('Program studi tidak ditemukan.');
        }

        if ($program['student_count'] + $program['lecturer_count'] + $program['course_count'] > 0) {
            return redirect()->back()->with('error', 'Program studi ' . $program['name'] . ' masih memiliki mahasiswa, dosen, atau mata kuliah.');
        }

        $this->programs->delete($id);

        return redirect()->to('admin/study-programs')->with('success', 'Program studi ' . $program['name'] . ' berhasil dihapus.');
    }

    private function findProgram(int $id): array
    {
        $program = $this->programs->find($id);

        if ($program === null) {
            throw PageNotFoundException::forPageNotFound('Program studi tidak ditemukan.');
        }

        return $program;
    }

    private function collectInput(): array
    {
        return [
            'code'   => strtoupper(trim((string) $this->request->getPost('code'))),
            'name'   => trim((string) $this->request->getPost('name')),
            'degree' => $this->request->getPost('degree'),
        ];
    }
}
