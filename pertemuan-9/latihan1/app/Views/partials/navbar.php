<nav class="navbar navbar-expand-lg navbar-dark bg-lablend sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= site_url('/') ?>">
            <i class="bi bi-eyedropper"></i> LabLend
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link<?= url_is('/') ? ' active' : '' ?>" href="<?= site_url('/') ?>">Beranda</a></li>
                <li class="nav-item"><a class="nav-link<?= url_is('tools*') ? ' active' : '' ?>" href="<?= site_url('tools') ?>">Katalog Alat</a></li>
                <li class="nav-item"><a class="nav-link<?= url_is('about') ? ' active' : '' ?>" href="<?= site_url('about') ?>">Tentang</a></li>
                <li class="nav-item"><a class="nav-link<?= url_is('contact') ? ' active' : '' ?>" href="<?= site_url('contact') ?>">Kontak</a></li>
                <li class="nav-item"><a class="nav-link<?= url_is('welcome') ? ' active' : '' ?>" href="<?= site_url('welcome') ?>">Welcome</a></li>
            </ul>
            <div class="d-flex gap-2">
                <?php if (session('user_role') === null) : ?>
                    <a class="btn btn-light btn-sm" href="<?= site_url('login') ?>"><i class="bi bi-box-arrow-in-right"></i> Masuk Mahasiswa</a>
                    <a class="btn btn-outline-light btn-sm" href="<?= site_url('admin/login') ?>">Laboran</a>
                <?php else : ?>
                    <a class="btn btn-light btn-sm" href="<?= site_url(session('user_role')) ?>"><i class="bi bi-speedometer2"></i> <?= esc(session('user_name')) ?></a>
                    <form action="<?= site_url('logout') ?>" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-light btn-sm">Keluar</button>
                    </form>
                <?php endif ?>
            </div>
        </div>
    </div>
</nav>
