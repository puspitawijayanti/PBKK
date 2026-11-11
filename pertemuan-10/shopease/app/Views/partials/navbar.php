<?php
$path      = trim(uri_string(), '/');
$user      = current_user();
$cartCount = $user !== null && $user['role'] === 'customer' ? (new \App\Services\CartService($user['id']))->count() : 0;
?>
<header class="sticky-top bg-white border-bottom shadow-sm">
    <nav class="navbar navbar-expand-lg" aria-label="Navigasi utama">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="<?= url_to('home') ?>">
                <span class="brand-mark"><i class="bi bi-bag-heart-fill"></i></span>
                <?= esc($shop->name) ?>
            </a>

            <div class="d-flex align-items-center gap-2 order-lg-last">
                <?php if ($user !== null && $user['role'] === 'customer') : ?>
                    <a class="btn btn-light position-relative" href="<?= url_to('cart') ?>" aria-label="Keranjang, <?= $cartCount ?> barang">
                        <i class="bi bi-cart3 fs-5"></i>
                        <?php if ($cartCount > 0) : ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-warning"><?= $cartCount ?></span>
                        <?php endif ?>
                    </a>
                <?php endif ?>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navUtama" aria-controls="navUtama" aria-expanded="false" aria-label="Buka menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="navUtama">
                <ul class="navbar-nav me-lg-3">
                    <li class="nav-item">
                        <a class="nav-link<?= $path === '' ? ' active' : '' ?>" href="<?= url_to('home') ?>"<?= $path === '' ? ' aria-current="page"' : '' ?>>Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= str_starts_with($path, 'products') ? ' active' : '' ?>" href="<?= url_to('products') ?>">Katalog</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Kategori</a>
                        <ul class="dropdown-menu">
                            <?php foreach (model(\App\Models\CategoryModel::class)->activeWithProductCount() as $category) : ?>
                                <li>
                                    <a class="dropdown-item" href="<?= url_to('category', $category['slug']) ?>">
                                        <i class="bi <?= esc($category['icon'], 'attr') ?> me-2"></i><?= esc($category['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </li>
                </ul>

                <form class="flex-grow-1 my-3 my-lg-0" role="search" action="<?= url_to('products') ?>" method="get">
                    <div class="input-group">
                        <label class="visually-hidden" for="cariProduk">Cari produk</label>
                        <input class="form-control" type="search" id="cariProduk" name="q" placeholder="Cari produk di <?= esc($shop->name, 'attr') ?>..." value="<?= esc((string) service('request')->getGet('q'), 'attr') ?>">
                        <button class="btn btn-primary" type="submit" aria-label="Cari"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <div class="d-flex gap-2 ms-lg-3 me-lg-2 pb-3 pb-lg-0">
                    <?php if ($user === null) : ?>
                        <a class="btn btn-outline-primary" href="<?= url_to('login') ?>">Masuk</a>
                        <a class="btn btn-primary" href="<?= url_to('register') ?>">Daftar</a>
                    <?php else : ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i><?= esc(strtok($user['name'], ' ')) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header"><?= esc($user['name']) ?> &middot; <?= role_label($user['role']) ?></h6></li>
                                <?php if ($user['role'] === 'customer') : ?>
                                    <li><a class="dropdown-item" href="<?= url_to('orders') ?>"><i class="bi bi-receipt me-2"></i>Pesanan Saya</a></li>
                                    <li><a class="dropdown-item" href="<?= url_to('cart') ?>"><i class="bi bi-cart3 me-2"></i>Keranjang</a></li>
                                <?php else : ?>
                                    <li><a class="dropdown-item" href="<?= $user['role'] === 'admin' ? url_to('admin.dashboard') : url_to('admin.orders') ?>"><i class="bi bi-speedometer2 me-2"></i>Panel Admin</a></li>
                                <?php endif ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="<?= url_to('logout') ?>" method="post">
                                        <?= csrf_field() ?>
                                        <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </nav>
</header>
