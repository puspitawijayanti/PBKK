<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'ShopEase') ?> | ShopEase</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/shopease.css') ?>">
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg se-nav sticky-top">
    <div class="container">
        <a class="navbar-brand se-brand" href="<?= site_url('/') ?>">Shop<span>Ease</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link <?= url_is('/') ? 'active' : '' ?>" href="<?= site_url('/') ?>">Beranda</a></li>
                <li class="nav-item"><a class="nav-link <?= url_is('catalog*') || url_is('product*') ? 'active' : '' ?>" href="<?= site_url('catalog') ?>">Katalog</a></li>
                <li class="nav-item"><a class="nav-link <?= url_is('about') ? 'active' : '' ?>" href="<?= site_url('about') ?>">Tentang</a></li>
                <li class="nav-item"><a class="nav-link <?= url_is('contact') ? 'active' : '' ?>" href="<?= site_url('contact') ?>">Kontak</a></li>
            </ul>

            <form class="d-flex me-lg-3 mb-2 mb-lg-0" action="<?= site_url('catalog') ?>" method="get" role="search">
                <input class="form-control form-control-sm me-2" type="search" name="q" placeholder="Cari produk..." aria-label="Cari produk" value="<?= esc((string) service('request')->getGet('q')) ?>">
                <button class="btn btn-sm btn-primary" type="submit">Cari</button>
            </form>

            <ul class="navbar-nav align-items-lg-center gap-lg-1">
                <?php if (! is_logged_in()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('login') ?>">Masuk</a></li>
                    <li class="nav-item"><a class="btn btn-sm btn-outline-primary" href="<?= site_url('register') ?>">Daftar</a></li>
                <?php elseif (current_role() === 'customer'): ?>
                    <?php $cc = cart_count(); ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('cart') ?>">Keranjang
                            <?php if ($cc > 0): ?><span class="badge rounded-pill bg-primary"><?= $cc ?></span><?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?= esc(session('name')) ?></a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= site_url('orders') ?>">Pesanan Saya</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('account/addresses') ?>">Alamat Pengiriman</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="<?= site_url('logout') ?>" method="post" class="px-3">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-link p-0 text-danger text-decoration-none" type="submit">Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="btn btn-sm btn-primary" href="<?= site_url('admin') ?>">Panel <?= esc(role_label(current_role())) ?></a></li>
                    <li class="nav-item">
                        <form action="<?= site_url('logout') ?>" method="post" class="ms-lg-2">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-outline-secondary" type="submit">Keluar</button>
                        </form>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4 flex-grow-1">
    <?= view('partials/flash') ?>
    <?= $this->renderSection('content') ?>
</main>

<footer class="footer border-top bg-white py-4 mt-4">
    <div class="container d-flex flex-column flex-md-row justify-content-between gap-2">
        <div>&copy; <?= date('Y') ?> ShopEase Online Store &middot; Proyek pembelajaran Web Framework (CodeIgniter 4)</div>
        <div><a class="text-decoration-none" href="<?= site_url('about') ?>">Tentang</a> &middot; <a class="text-decoration-none" href="<?= site_url('contact') ?>">Kontak</a></div>
    </div>
</footer>

<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
