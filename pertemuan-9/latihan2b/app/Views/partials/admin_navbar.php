<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3" type="button" aria-label="Buka menu">
        <i class="fa fa-bars"></i>
    </button>

    <span class="d-none d-sm-inline text-gray-600 small">
        Panel Admin Toko &middot; <?= date_id(date('Y-m-d')) ?>
    </span>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= esc(session('user_name')) ?></span>
                <img class="img-profile rounded-circle" src="<?= base_url('assets/sb-admin-2/img/undraw_profile.svg') ?>" alt="Foto profil">
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <span class="dropdown-item-text small text-muted"><?= esc(role_label((string) session('user_role'))) ?></span>
                <div class="dropdown-divider"></div>
                <form action="<?= site_url('logout') ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Keluar
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>
