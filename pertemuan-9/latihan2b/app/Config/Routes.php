<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'HomeController::index');
$routes->get('products', 'ProductController::index');
$routes->get('products/(:segment)', 'ProductController::show/$1');

$routes->get('cart', 'CartController::index');
$routes->post('cart', 'CartController::add');
$routes->post('cart/update', 'CartController::update');
$routes->post('cart/(:num)/remove', 'CartController::remove/$1');

$routes->get('login', 'AuthController::loginForm');
$routes->post('login', 'AuthController::login');
$routes->get('register', 'AuthController::registerForm');
$routes->post('register', 'AuthController::register');
$routes->post('logout', 'AuthController::logout', ['filter' => 'auth']);

$routes->group('', ['filter' => ['auth', 'role:buyer'], 'namespace' => 'App\Controllers\Buyer'], static function (RouteCollection $routes): void {
    $routes->get('checkout', 'CheckoutController::index');
    $routes->post('checkout', 'CheckoutController::store');
    $routes->get('orders', 'OrderController::index');
    $routes->get('orders/(:segment)', 'OrderController::show/$1');
    $routes->get('orders/(:segment)/proof', 'OrderController::proof/$1');
    $routes->post('orders/(:segment)/payment', 'OrderController::uploadPayment/$1');
    $routes->post('orders/(:segment)/cancel', 'OrderController::cancel/$1');
    $routes->post('orders/(:segment)/complete', 'OrderController::complete/$1');
});

$routes->group('admin', ['filter' => ['auth', 'role:admin'], 'namespace' => 'App\Controllers\Admin'], static function (RouteCollection $routes): void {
    $routes->get('/', 'DashboardController::index');

    foreach (['categories' => 'CategoryController', 'products' => 'ProductController'] as $segment => $controller) {
        $routes->get($segment, $controller . '::index');
        $routes->get($segment . '/new', $controller . '::new');
        $routes->post($segment, $controller . '::create');
        $routes->get($segment . '/(:num)/edit', $controller . '::edit/$1');
        $routes->post($segment . '/(:num)', $controller . '::update/$1');
        $routes->post($segment . '/(:num)/delete', $controller . '::delete/$1');
    }

    $routes->get('orders', 'OrderController::index');
    $routes->get('orders/(:num)', 'OrderController::show/$1');
    $routes->get('orders/(:num)/proof', 'OrderController::proof/$1');
    $routes->post('orders/(:num)/status', 'OrderController::updateStatus/$1');
});
