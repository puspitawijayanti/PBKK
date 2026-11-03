<?php

namespace App\Controllers;

use CodeIgniter\CodeIgniter;
use Config\Site;

class WelcomeController extends BaseController
{
    public function index(): string
    {
        $site = config(Site::class);

        return view('welcome/index', [
            'title'     => 'Selamat Datang',
            'site'      => $site,
            'biodata'   => [
                'Nama'         => $site->studentName,
                'NRP'          => $site->studentNumber,
                'Kelas'        => $site->className,
                'Mata Kuliah'  => $site->courseName,
            ],
            'runtime'   => [
                'Versi CodeIgniter' => CodeIgniter::CI_VERSION,
                'Versi PHP'         => PHP_VERSION,
                'Environment'       => ENVIRONMENT,
                'Base URL'          => base_url(),
                'Zona Waktu'        => app_timezone(),
                'Database'          => db_connect()->getDatabase(),
            ],
            'directories' => [
                'app/'      => 'Kode aplikasi: Config, Controllers, Models, Views, Filters, Database.',
                'public/'   => 'Document root web server: index.php, aset CSS/JS, dan folder uploads.',
                'writable/' => 'Folder yang dapat ditulis: cache, logs, session, dan file sementara.',
                'vendor/'   => 'Dependensi Composer, termasuk inti framework di codeigniter4/framework.',
                'tests/'    => 'Kerangka pengujian PHPUnit.',
                '.env'      => 'Konfigurasi lingkungan: baseURL, database, zona waktu.',
            ],
        ]);
    }
}
