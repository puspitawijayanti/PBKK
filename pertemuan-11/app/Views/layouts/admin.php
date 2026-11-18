<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Admin') ?> | ShopEase Admin</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/shopease.css') ?>">
</head>
<body>
<?php $isAdmin = current_role() === 'admin'; ?>
<nav class="navbar se-nav sticky-top">
    <div class="container-fluid px-lg-4">
        <a class="navbar-brand se-brand" href="<?= site_url('admin') ?>">Shop<span>Ease</span> <small class="badge bg-primary align-middle fs-6"><?= $isAdmin ? 'Admin' : 'Operator' ?></small></a>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small d-none d-md-inline"><?= esc(session('name')) ?> &middot; <?= esc(role_label((string) current_role())) ?></span>
            <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('/') ?>" target="_blank" rel="noopener">Lihat Toko</a>
            <form action="<?= site_url('logout') ?>" method="post">
                <?= csrf_field() ?>
                <button class="btn btn-sm btn-outline-danger" type="submit">Keluar</button>
            </form>
        </div>
    </div>
</nav>

<div class="container-fluid px-lg-4 py-4">
    <div class="row g-4">
        <aside class="col-lg-2">
            <div class="admin-side">
                <ul class="nav flex-column">
                    <li class="nav-link-wrap"><a class="nav-link <?= url_is('admin') || url_is('admin/dashboard') ? 'active' : '' ?>" href="<?= site_url('admin') ?>">Dashboard</a></li>
                    <li class="nav-heading">Transaksi</li>
                    <li><a class="nav-link <?= url_is('admin/orders*') ? 'active' : '' ?>" href="<?= site_url('admin/orders') ?>">Pesanan</a></li>
                    <?php if ($isAdmin): ?>
                        <li><a class="nav-link <?= url_is('admin/payments*') ? 'active' : '' ?>" href="<?= site_url('admin/payments') ?>">Pembayaran</a></li>
                    <?php endif; ?>
                    <li><a class="nav-link <?= url_is('admin/shipments*') ? 'active' : '' ?>" href="<?= site_url('admin/shipments') ?>">Pengiriman</a></li>
                    <li class="nav-heading">Katalog</li>
                    <?php if ($isAdmin): ?>
                        <li><a class="nav-link <?= url_is('admin/categories*') ? 'active' : '' ?>" href="<?= site_url('admin/categories') ?>">Kategori</a></li>
                        <li><a class="nav-link <?= url_is('admin/products*') ? 'active' : '' ?>" href="<?= site_url('admin/products') ?>">Produk</a></li>
                    <?php endif; ?>
                    <li><a class="nav-link <?= url_is('admin/stock*') ? 'active' : '' ?>" href="<?= site_url('admin/stock') ?>">Stok</a></li>
                    <?php if ($isAdmin): ?>
                        <li class="nav-heading">Lainnya</li>
                        <li><a class="nav-link <?= url_is('admin/customers*') ? 'active' : '' ?>" href="<?= site_url('admin/customers') ?>">Pelanggan</a></li>
                        <li><a class="nav-link <?= url_is('admin/reports*') ? 'active' : '' ?>" href="<?= site_url('admin/reports') ?>">Laporan</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </aside>
        <main class="col-lg-10">
            <?= view('partials/flash') ?>
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>

<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
