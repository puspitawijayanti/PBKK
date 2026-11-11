<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Rute ShopEase. Hak akses diatur dengan filter `auth` (wajib login) dan `role:<role>` (FR-004).
 *
 * @var RouteCollection $routes
 */

// Halaman publik (PRD bagian 10)
$routes->get('/', 'HomeController::index', ['as' => 'home']);
$routes->get('products', 'ProductController::index', ['as' => 'products']);
$routes->get('products/(:segment)', 'ProductController::show/$1', ['as' => 'product.show']);
$routes->get('category/(:segment)', 'ProductController::category/$1', ['as' => 'category']);

// Autentikasi (FR-001 s.d. FR-003)
$routes->get('login', 'AuthController::login', ['as' => 'login']);
$routes->post('login', 'AuthController::attemptLogin');
$routes->get('register', 'AuthController::register', ['as' => 'register']);
$routes->post('register', 'AuthController::attemptRegister');
$routes->post('logout', 'AuthController::logout', ['as' => 'logout', 'filter' => 'auth']);

// Customer: keranjang, checkout, dan pesanan (BR-001: checkout hanya untuk customer yang login)
$routes->group('', ['filter' => ['auth', 'role:customer'], 'namespace' => 'App\Controllers\Customer'], static function (RouteCollection $routes): void {
    $routes->get('cart', 'CartController::index', ['as' => 'cart']);
    $routes->post('cart', 'CartController::add', ['as' => 'cart.add']);
    $routes->post('cart/(:num)', 'CartController::update/$1', ['as' => 'cart.update']);
    $routes->post('cart/(:num)/remove', 'CartController::remove/$1', ['as' => 'cart.remove']);

    $routes->get('checkout', 'CheckoutController::index', ['as' => 'checkout']);
    $routes->post('checkout', 'CheckoutController::store', ['as' => 'checkout.store']);
    $routes->post('addresses', 'AddressController::create', ['as' => 'address.create']);
    $routes->post('addresses/(:num)/delete', 'AddressController::delete/$1', ['as' => 'address.delete']);

    $routes->get('orders', 'OrderController::index', ['as' => 'orders']);
    $routes->get('orders/(:segment)', 'OrderController::show/$1', ['as' => 'order.show']);
    $routes->get('orders/(:segment)/proof', 'OrderController::proof/$1', ['as' => 'order.proof']);
    $routes->post('orders/(:segment)/payment', 'OrderController::uploadPayment/$1', ['as' => 'order.payment']);
    $routes->post('orders/(:segment)/cancel', 'OrderController::cancel/$1', ['as' => 'order.cancel']);
    $routes->post('orders/(:segment)/complete', 'OrderController::complete/$1', ['as' => 'order.complete']);
});

// Panel admin dan operator
$routes->group('admin', ['filter' => 'auth', 'namespace' => 'App\Controllers\Admin'], static function (RouteCollection $routes): void {
    // Admin dan operator: pemenuhan pesanan (FR-016)
    $routes->group('', ['filter' => 'role:admin,operator'], static function (RouteCollection $routes): void {
        $routes->get('orders', 'OrderController::index', ['as' => 'admin.orders']);
        $routes->get('orders/(:num)', 'OrderController::show/$1', ['as' => 'admin.order.show']);
        $routes->get('orders/(:num)/proof', 'OrderController::proof/$1', ['as' => 'admin.order.proof']);
        $routes->post('orders/(:num)/ship', 'OrderController::ship/$1', ['as' => 'admin.order.ship']);
        $routes->post('orders/(:num)/complete', 'OrderController::complete/$1', ['as' => 'admin.order.complete']);
    });

    // Khusus admin: dashboard, katalog, verifikasi pembayaran, pengguna, laporan
    $routes->group('', ['filter' => 'role:admin'], static function (RouteCollection $routes): void {
        $routes->get('/', 'DashboardController::index', ['as' => 'admin.dashboard']);
        $routes->get('reports', 'ReportController::index', ['as' => 'admin.reports']);

        $routes->post('orders/(:num)/verify', 'OrderController::verify/$1', ['as' => 'admin.order.verify']);
        $routes->post('orders/(:num)/cancel', 'OrderController::cancel/$1', ['as' => 'admin.order.cancel']);

        // CRUD kategori dan produk (FR-005, FR-006)
        foreach (['categories' => ['CategoryController', 'category'], 'products' => ['ProductController', 'product']] as $segment => [$controller, $name]) {
            $routes->get($segment, $controller . '::index', ['as' => 'admin.' . $segment]);
            $routes->get($segment . '/new', $controller . '::new', ['as' => 'admin.' . $name . '.new']);
            $routes->post($segment, $controller . '::create', ['as' => 'admin.' . $name . '.create']);
            $routes->get($segment . '/(:num)/edit', $controller . '::edit/$1', ['as' => 'admin.' . $name . '.edit']);
            $routes->post($segment . '/(:num)', $controller . '::update/$1', ['as' => 'admin.' . $name . '.update']);
            $routes->post($segment . '/(:num)/delete', $controller . '::delete/$1', ['as' => 'admin.' . $name . '.delete']);
        }

        $routes->get('users', 'UserController::index', ['as' => 'admin.users']);
        $routes->post('users/(:num)/toggle', 'UserController::toggle/$1', ['as' => 'admin.user.toggle']);
    });
});
