<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LecturerModel;
use App\Models\StudentModel;
use App\Models\StudyProgramModel;
use CodeIgniter\HTTP\RedirectResponse;

class AdvisorController extends BaseController
{
    public function index(): string
    {
        $programId = (int) $this->request->getGet('program');
        $onlyUnassigned = $this->request->getGet('unassigned') === '1';
        $query = model(StudentModel::class)->search(null, $programId);

        if ($onlyUnassigned) {
            $query->where('students.advisor_id', null);
        }

        return view('admin/advisors/index', [
            'title'          => 'Atur Dosen Wali',
            'students'       => $query->paginate(15),
            'pager'          => $query->pager,
            'programs'       => model(StudyProgramModel::class)->options(),
            'lecturers'      => model(LecturerModel::class)->withDetails()->orderBy('users.name')->findAll(),
            'programId'      => $programId,
            'onlyUnassigned' => $onlyUnassigned,
        ]);
    }

    public function update(): RedirectResponse
    {
        $assignments = (array) $this->request->getPost('advisor');
        $validLecturerIds = array_map('intval', array_column(model(LecturerModel::class)->select('id')->findAll(), 'id'));
        $students = model(StudentModel::class);
        $changed = 0;

        $db = db_connect();
        $db->transStart();

        foreach ($assignments as $studentId => $lecturerId) {
            $lecturerId = $lecturerId === '' ? null : (int) $lecturerId;

            if ($lecturerId !== null && ! in_array($lecturerId, $validLecturerIds, true)) {
                continue;
            }

            $student = $students->find((int) $studentId);

            if ($student === null || $student['advisor_id'] === ($lecturerId === null ? null : (string) $lecturerId)) {
                continue;
            }

            $students->update((int) $studentId, ['advisor_id' => $lecturerId]);
            $changed++;
        }

        $db->transComplete();

        return redirect()->back()->with('success', $changed > 0 ? $changed . ' penetapan dosen wali berhasil disimpan.' : 'Tidak ada perubahan dosen wali.');
    }
}
