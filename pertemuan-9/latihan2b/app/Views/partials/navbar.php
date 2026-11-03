<nav class="navbar navbar-expand-lg navbar-dark bg-coffee sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= site_url('/') ?>"><i class="bi bi-cup-hot-fill"></i> Kopi Nusantara</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link<?= url_is('/') ? ' active' : '' ?>" href="<?= site_url('/') ?>">Beranda</a></li>
                <li class="nav-item"><a class="nav-link<?= url_is('products*') ? ' active' : '' ?>" href="<?= site_url('products') ?>">Katalog</a></li>
                <?php if (session('user_role') === 'buyer') : ?>
                    <li class="nav-item"><a class="nav-link<?= url_is('orders*') ? ' active' : '' ?>" href="<?= site_url('orders') ?>">Pesanan Saya</a></li>
                <?php endif ?>
            </ul>
            <form action="<?= site_url('products') ?>" method="get" class="d-flex me-lg-3 mb-2 mb-lg-0" role="search">
                <input class="form-control form-control-sm" type="search" name="q" placeholder="Cari kopi atau daerah asal" aria-label="Cari produk" value="<?= esc((string) service('request')->getGet('q'), 'attr') ?>">
            </form>
            <div class="d-flex gap-2 align-items-center">
                <a href="<?= site_url('cart') ?>" class="btn btn-sm btn-light position-relative" aria-label="Keranjang">
                    <i class="bi bi-bag"></i> Keranjang
                    <?php if (cart_count() > 0) : ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark"><?= cart_count() ?></span>
                    <?php endif ?>
                </a>
                <?php if (session('user_role') === null) : ?>
                    <a class="btn btn-sm btn-outline-light" href="<?= site_url('login') ?>">Masuk</a>
                    <a class="btn btn-sm btn-warning" href="<?= site_url('register') ?>">Daftar</a>
                <?php else : ?>
                    <?php if (session('user_role') === 'admin') : ?>
                        <a class="btn btn-sm btn-outline-light" href="<?= site_url('admin') ?>"><i class="bi bi-speedometer2"></i> Admin</a>
                    <?php endif ?>
                    <span class="text-white-50 small d-none d-xl-inline"><?= esc(session('user_name')) ?></span>
                    <form action="<?= site_url('logout') ?>" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-light">Keluar</button>
                    </form>
                <?php endif ?>
            </div>
        </div>
    </div>
</nav>
