<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Services\GradeService;

class ResultController extends BaseController
{
    public function index(): string
    {
        return view('student/results/index', $this->pageData('Kartu Hasil Studi (KHS)'));
    }

    public function print(): string
    {
        return view('student/results/print', $this->pageData('Cetak KHS'));
    }

    private function pageData(string $title): array
    {
        $student = $this->currentStudent();
        $enrollments = model(EnrollmentModel::class);
        $grades = new GradeService();
        $periods = $enrollments->periodsForStudent((int) $student['id']);

        $year = (string) $this->request->getGet('year');
        $term = (string) $this->request->getGet('term');
        $valid = array_filter($periods, static fn (array $period): bool => $period['academic_year'] === $year && $period['term'] === $term);

        if ($valid === [] && $periods !== []) {
            $year = $periods[0]['academic_year'];
            $term = $periods[0]['term'];
        }

        $rows = $year === '' ? [] : $enrollments->forPeriod((int) $student['id'], $year, $term);
        $all = $enrollments->allForStudent((int) $student['id']);
        $history = [];

        foreach ($periods as $period) {
            $history[] = $period + ['summary' => $grades->summarize(array_filter(
                $all,
                static fn (array $row): bool => $row['academic_year'] === $period['academic_year'] && $row['term'] === $period['term']
            ))];
        }

        return [
            'title'      => $title,
            'student'    => $student,
            'periods'    => $periods,
            'history'    => $history,
            'year'       => $year,
            'term'       => $term,
            'rows'       => $rows,
            'semester'   => $grades->summarize($rows),
            'cumulative' => $grades->summarize($all),
            'config'     => config('Academic'),
        ];
    }
}
