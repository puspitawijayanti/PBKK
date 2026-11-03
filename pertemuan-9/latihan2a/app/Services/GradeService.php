<?php

namespace App\Services;

use Config\Academic;

class GradeService
{
    private Academic $config;

    public function __construct()
    {
        $this->config = config(Academic::class);
    }

    public function fromScore(float $score): array
    {
        foreach ($this->config->gradeScale as $level) {
            if ($score >= $level['min']) {
                return ['grade' => $level['grade'], 'weight' => $level['weight']];
            }
        }

        return ['grade' => 'E', 'weight' => 0.0];
    }

    public function summarize(array $enrollments): array
    {
        $totalCredits = 0;
        $gradedCredits = 0;
        $qualityPoints = 0.0;

        foreach ($enrollments as $enrollment) {
            $credits = (int) $enrollment['credits'];
            $totalCredits += $credits;

            if ($enrollment['grade'] !== null) {
                $gradedCredits += $credits;
                $qualityPoints += $credits * (float) $enrollment['weight'];
            }
        }

        return [
            'totalCredits'  => $totalCredits,
            'gradedCredits' => $gradedCredits,
            'qualityPoints' => $qualityPoints,
            'gpa'           => $gradedCredits > 0 ? round($qualityPoints / $gradedCredits, 2) : null,
        ];
    }
}
