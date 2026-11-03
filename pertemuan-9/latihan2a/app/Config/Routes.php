<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'AuthController::home');
$routes->get('login', 'AuthController::loginForm');
$routes->post('login', 'AuthController::login');
$routes->post('logout', 'AuthController::logout', ['filter' => 'auth']);

$routes->group('admin', ['filter' => ['auth', 'role:admin'], 'namespace' => 'App\Controllers\Admin'], static function (RouteCollection $routes): void {
    $routes->get('/', 'DashboardController::index');

    foreach (['study-programs' => 'StudyProgramController', 'lecturers' => 'LecturerController', 'students' => 'StudentController', 'courses' => 'CourseController'] as $segment => $controller) {
        $routes->get($segment, $controller . '::index');
        $routes->get($segment . '/new', $controller . '::new');
        $routes->post($segment, $controller . '::create');
        $routes->get($segment . '/(:num)/edit', $controller . '::edit/$1');
        $routes->post($segment . '/(:num)', $controller . '::update/$1');
        $routes->post($segment . '/(:num)/delete', $controller . '::delete/$1');
    }

    $routes->get('advisors', 'AdvisorController::index');
    $routes->post('advisors', 'AdvisorController::update');

    $routes->get('users', 'UserController::index');
    $routes->post('users/(:num)/reset-password', 'UserController::resetPassword/$1');
});

$routes->group('lecturer', ['filter' => ['auth', 'role:lecturer'], 'namespace' => 'App\Controllers\Lecturer'], static function (RouteCollection $routes): void {
    $routes->get('/', 'DashboardController::index');
    $routes->get('classes', 'ClassController::index');
    $routes->get('classes/(:num)', 'ClassController::show/$1');
    $routes->post('classes/(:num)/scores', 'ClassController::saveScores/$1');
    $routes->get('advisees', 'AdviseeController::index');
    $routes->get('advisees/(:num)', 'AdviseeController::show/$1');
});

$routes->group('student', ['filter' => ['auth', 'role:student'], 'namespace' => 'App\Controllers\Student'], static function (RouteCollection $routes): void {
    $routes->get('/', 'DashboardController::index');
    $routes->get('enrollments', 'EnrollmentController::index');
    $routes->post('enrollments', 'EnrollmentController::create');
    $routes->post('enrollments/(:num)/delete', 'EnrollmentController::delete/$1');
    $routes->get('enrollments/print', 'EnrollmentController::print');
    $routes->get('results', 'ResultController::index');
    $routes->get('results/print', 'ResultController::print');
});
