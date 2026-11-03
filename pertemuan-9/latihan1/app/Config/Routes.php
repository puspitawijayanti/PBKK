<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'HomeController::index');
$routes->get('welcome', 'WelcomeController::index');
$routes->get('about', 'PageController::about');
$routes->get('contact', 'PageController::contact');
$routes->post('contact', 'PageController::sendMessage');
$routes->get('tools', 'ToolController::index');
$routes->get('tools/(:segment)', 'ToolController::show/$1');

$routes->get('login', 'AuthController::loginForm');
$routes->post('login', 'AuthController::login');
$routes->get('admin/login', 'AuthController::adminLoginForm');
$routes->post('admin/login', 'AuthController::adminLogin');
$routes->post('logout', 'AuthController::logout');

$routes->group('admin', ['filter' => ['auth', 'role:admin'], 'namespace' => 'App\Controllers\Admin'], static function (RouteCollection $routes): void {
    $routes->get('/', 'DashboardController::index');

    $routes->get('tools', 'ToolController::index');
    $routes->get('tools/new', 'ToolController::new');
    $routes->post('tools', 'ToolController::create');
    $routes->get('tools/(:num)/edit', 'ToolController::edit/$1');
    $routes->post('tools/(:num)', 'ToolController::update/$1');
    $routes->post('tools/(:num)/delete', 'ToolController::delete/$1');

    $routes->get('categories', 'CategoryController::index');
    $routes->get('categories/new', 'CategoryController::new');
    $routes->post('categories', 'CategoryController::create');
    $routes->get('categories/(:num)/edit', 'CategoryController::edit/$1');
    $routes->post('categories/(:num)', 'CategoryController::update/$1');
    $routes->post('categories/(:num)/delete', 'CategoryController::delete/$1');

    $routes->get('users', 'UserController::index');
    $routes->get('users/new', 'UserController::new');
    $routes->post('users', 'UserController::create');
    $routes->get('users/(:num)/edit', 'UserController::edit/$1');
    $routes->post('users/(:num)', 'UserController::update/$1');
    $routes->post('users/(:num)/delete', 'UserController::delete/$1');

    $routes->get('loans', 'LoanController::index');
    $routes->post('loans/(:num)/status', 'LoanController::updateStatus/$1');

    $routes->get('messages', 'MessageController::index');
    $routes->post('messages/(:num)/delete', 'MessageController::delete/$1');
});

$routes->group('borrower', ['filter' => ['auth', 'role:borrower'], 'namespace' => 'App\Controllers\Borrower'], static function (RouteCollection $routes): void {
    $routes->get('/', 'DashboardController::index');
    $routes->get('loans', 'LoanController::index');
    $routes->get('loans/new/(:num)', 'LoanController::new/$1');
    $routes->post('loans', 'LoanController::create');
    $routes->get('loans/(:num)', 'LoanController::show/$1');
    $routes->post('loans/(:num)/cancel', 'LoanController::cancel/$1');
});
