<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Academic extends BaseConfig
{
    public string $institutionName = 'Universitas Nusantara Raya';
    public string $currentAcademicYear = '2026/2027';
    public string $currentTerm = 'odd';
    public bool $enrollmentOpen = true;
    public int $maxCredits = 24;
    public int $defaultQuota = 40;

    public array $gradeScale = [
        ['min' => 86, 'grade' => 'A', 'weight' => 4.0],
        ['min' => 76, 'grade' => 'AB', 'weight' => 3.5],
        ['min' => 66, 'grade' => 'B', 'weight' => 3.0],
        ['min' => 61, 'grade' => 'BC', 'weight' => 2.5],
        ['min' => 56, 'grade' => 'C', 'weight' => 2.0],
        ['min' => 41, 'grade' => 'D', 'weight' => 1.0],
        ['min' => 0, 'grade' => 'E', 'weight' => 0.0],
    ];
}
