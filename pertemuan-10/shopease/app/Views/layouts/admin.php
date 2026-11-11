<?php
$shop = config(\Config\Shop::class);
$user = current_user();
$path = trim(uri_string(), '/');
$menu = [
    ['admin.dashboard', 'admin', 'bi-speedometer2', 'Dashboard', ['admin']],
    ['admin.orders', 'admin/orders', 'bi-receipt', 'Pesanan', ['admin', 'operator']],
    ['admin.products', 'admin/products', 'bi-box-seam', 'Produk', ['admin']],
    ['admin.categories', 'admin/categories', 'bi-tags', 'Kategori', ['admin']],
    ['admin.users', 'admin/users', 'bi-people', 'Pengguna', ['admin']],
    ['admin.reports', 'admin/reports', 'bi-graph-up', 'Laporan', ['admin']],
];
$isActive = static fn (string $prefix): bool => $prefix === 'admin' ? $path === 'admin' : str_starts_with($path, $prefix);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Panel') ?> | Panel <?= esc($shop->name) ?></title>
    <link rel="icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap5/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/shopease.css') ?>">
</head>
<body class="admin-body">
    <a class="visually-hidden-focusable position-absolute top-0 start-0 m-2 btn btn-light" href="#konten">Lewati ke konten</a>

    <aside class="offcanvas-lg offcanvas-start admin-sidebar" tabindex="-1" id="adminSidebar" aria-label="Menu panel">
        <div class="offcanvas-header">
            <span class="fw-bold text-white">Menu</span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" aria-label="Tutup menu"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column p-0">
            <a class="d-flex align-items-center gap-2 fw-bold fs-5 text-white text-decoration-none px-4 py-3" href="<?= url_to('home') ?>">
                <span class="brand-mark"><i class="bi bi-bag-heart-fill"></i></span><?= esc($shop->name) ?>
            </a>
            <nav class="nav flex-column px-2">
                <?php foreach ($menu as [$route, $prefix, $icon, $label, $roles]) : ?>
                    <?php if (in_array($user['role'], $roles, true)) : ?>
                        <a class="nav-link<?= $isActive($prefix) ? ' active' : '' ?>" href="<?= url_to($route) ?>"<?= $isActive($prefix) ? ' aria-current="page"' : '' ?>>
                            <i class="bi <?= $icon ?> me-2"></i><?= $label ?>
                        </a>
                    <?php endif ?>
                <?php endforeach ?>
                <hr class="border-secondary mx-2">
                <a class="nav-link" href="<?= url_to('home') ?>" target="_blank" rel="noopener"><i class="bi bi-shop me-2"></i>Lihat toko</a>
            </nav>
        </div>
    </aside>

    <div class="admin-main d-flex flex-column min-vh-100">
        <header class="bg-white border-bottom px-3 px-lg-4 py-2 d-flex align-items-center gap-2 sticky-top">
            <button class="btn btn-light d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar" aria-label="Buka menu">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h1 class="h5 fw-bold mb-0 me-auto"><?= esc($title ?? '') ?></h1>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-1"></i><span class="d-none d-sm-inline"><?= esc($user['name']) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header"><?= role_label($user['role']) ?></h6></li>
                    <li>
                        <form action="<?= url_to('logout') ?>" method="post">
                            <?= csrf_field() ?>
                            <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <main id="konten" class="flex-grow-1 p-3 p-lg-4">
            <?= view('partials/flash') ?>
            <?= $this->renderSection('content') ?>
        </main>

        <footer class="small text-body-secondary px-4 py-3">&copy; <?= date('Y') ?> <?= esc($shop->name) ?> &middot; Panel <?= role_label($user['role']) ?></footer>
    </div>

    <script src="<?= base_url('assets/bootstrap5/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/shopease.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
