<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Site extends BaseConfig
{
    public string $appName = 'LabLend';
    public string $tagline = 'Peminjaman Alat Laboratorium Kampus';
    public string $studentName = '';
    public string $studentNumber = '';
    public string $className = '';
    public string $courseName = 'Pemrograman Berbasis Kerangka Kerja';
    public string $email = 'laboran@lablend.ac.id';
    public string $phone = '(031) 599-0000';
    public string $address = 'Gedung Laboratorium Terpadu Lantai 2, Kampus Sukolilo, Surabaya';
    public string $officeHours = 'Senin - Jumat, 08.00 - 16.00 WIB';
    public int $maxLoanDays = 14;
    public int $maxLoanQuantity = 5;
}
