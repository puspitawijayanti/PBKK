<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ------------------------------------------------------------------
// PUBLIK
// ------------------------------------------------------------------
$routes->get('/', 'HomeController::index');
$routes->get('about', 'HomeController::about');
$routes->get('contact', 'HomeController::contact');

$routes->get('catalog', 'ProductController::catalog');
$routes->get('product/(:segment)', 'ProductController::detail/$1');

// Autentikasi
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::attemptLogin');
$routes->get('register', 'AuthController::register');
$routes->post('register', 'AuthController::attemptRegister');
$routes->post('logout', 'AuthController::logout');

// ------------------------------------------------------------------
// CUSTOMER (harus login sebagai customer)
// ------------------------------------------------------------------
$routes->group('', ['filter' => 'auth:customer'], static function ($routes) {
    // Keranjang
    $routes->get('cart', 'CartController::index');
    $routes->post('cart/add', 'CartController::add');
    $routes->post('cart/update/(:num)', 'CartController::update/$1');
    $routes->post('cart/remove/(:num)', 'CartController::remove/$1');

    // Checkout
    $routes->get('checkout', 'CheckoutController::index');
    $routes->post('checkout', 'CheckoutController::place');

    // Pesanan & pembayaran
    $routes->get('orders', 'OrderController::index');
    $routes->get('orders/(:num)', 'OrderController::show/$1');
    $routes->post('orders/(:num)/cancel', 'OrderController::cancel/$1');
    $routes->post('orders/(:num)/received', 'OrderController::received/$1');
    $routes->post('orders/(:num)/payment', 'PaymentController::upload/$1');

    // Alamat pengiriman
    $routes->get('account/addresses', 'AddressController::index');
    $routes->get('account/addresses/create', 'AddressController::create');
    $routes->post('account/addresses', 'AddressController::store');
    $routes->get('account/addresses/(:num)/edit', 'AddressController::edit/$1');
    $routes->post('account/addresses/(:num)', 'AddressController::update/$1');
    $routes->post('account/addresses/(:num)/delete', 'AddressController::delete/$1');
    $routes->post('account/addresses/(:num)/default', 'AddressController::makeDefault/$1');
});

// Bukti pembayaran: hanya pemilik pesanan atau admin (dicek di controller)
$routes->get('payment/proof/(:num)', 'PaymentController::proof/$1', ['filter' => 'auth']);

// ------------------------------------------------------------------
// ADMIN + OPERATOR
// ------------------------------------------------------------------
$routes->group('admin', ['filter' => 'auth:admin,operator', 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'DashboardController::index');
    $routes->get('dashboard', 'DashboardController::index');

    // Pesanan (lihat & proses pengiriman)
    $routes->get('orders', 'OrderController::index');
    $routes->get('orders/(:num)', 'OrderController::show/$1');
    $routes->post('orders/(:num)/ship', 'OrderController::ship/$1');
    $routes->post('orders/(:num)/complete', 'OrderController::complete/$1');

    // Antrean pengiriman
    $routes->get('shipments', 'ShipmentController::index');

    // Stok
    $routes->get('stock', 'StockController::index');
    $routes->post('stock/(:num)', 'StockController::update/$1');
});

// ------------------------------------------------------------------
// ADMIN SAJA
// ------------------------------------------------------------------
$routes->group('admin', ['filter' => 'auth:admin', 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    // Kategori
    $routes->get('categories', 'CategoryController::index');
    $routes->get('categories/create', 'CategoryController::create');
    $routes->post('categories', 'CategoryController::store');
    $routes->get('categories/(:num)/edit', 'CategoryController::edit/$1');
    $routes->post('categories/(:num)', 'CategoryController::update/$1');
    $routes->post('categories/(:num)/delete', 'CategoryController::delete/$1');

    // Produk
    $routes->get('products', 'ProductController::index');
    $routes->get('products/create', 'ProductController::create');
    $routes->post('products', 'ProductController::store');
    $routes->get('products/(:num)/edit', 'ProductController::edit/$1');
    $routes->post('products/(:num)', 'ProductController::update/$1');
    $routes->post('products/(:num)/delete', 'ProductController::delete/$1');

    // Pelanggan
    $routes->get('customers', 'CustomerController::index');
    $routes->get('customers/(:num)', 'CustomerController::show/$1');

    // Pembayaran (verifikasi hanya admin - BR-007)
    $routes->get('payments', 'PaymentController::index');
    $routes->post('payments/(:num)/verify', 'PaymentController::verify/$1');
    $routes->post('payments/(:num)/reject', 'PaymentController::reject/$1');

    // Pembatalan pesanan (tanpa hapus fisik - BR-011)
    $routes->post('orders/(:num)/cancel', 'OrderController::cancel/$1');

    // Laporan
    $routes->get('reports', 'ReportController::index');
});
